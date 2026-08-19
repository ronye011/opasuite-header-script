<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OpaSuite_Admin {

	private $option_key;

	public function __construct( $option_key ) {
		$this->option_key = $option_key;
	}

	/**
	 * Add admin menu page
	 */
	public function add_admin_menu() {
		// Top level menu item
		add_menu_page(
			__( 'Opa! Suite - Webchat', 'opa-suite-webchat' ),
			'Opa! Suite - Webchat',
			'manage_options',
			'opasuite-header-script',
			array( $this, 'render_admin_page' ),
			'dashicons-code-standards',
			81
		);

		// Also register under Settings
		add_options_page(
			__( 'Opa! Suite - Webchat Settings', 'opa-suite-webchat' ),
			'Opa! Suite - Webchat',
			'manage_options',
			'opasuite-header-script',
			array( $this, 'render_admin_page' )
		);
	}

	/**
	 * Register settings with sanitize callback
	 */
	public function register_settings() {
		register_setting(
			'opasuite_options_group',
			$this->option_key,
			array( $this, 'sanitize_options' )
		);
	}

	/**
	 * Enqueue admin styles and scripts
	 */
	public function enqueue_assets( $hook ) {
		// Only load on plugin page
		if ( strpos( $hook, 'opasuite-header-script' ) === false ) {
			return;
		}

		wp_enqueue_style(
			'opasuite-admin-css',
			OPASUITE_HS_URL . 'assets/admin-style.css',
			array(),
			OPASUITE_HS_VERSION
		);

		wp_enqueue_script(
			'opasuite-admin-js',
			OPASUITE_HS_URL . 'assets/admin-script.js',
			array( 'jquery' ),
			OPASUITE_HS_VERSION,
			true
		);
	}

	/**
	 * Sanitize and validate options on save
	 */
	public function sanitize_options( $input ) {
		$output = array();

		$output['enabled'] = isset( $input['enabled'] ) && '1' === $input['enabled'] ? '1' : '0';

		$output['domain'] = ! empty( $input['domain'] )
			? esc_url_raw( trim( $input['domain'] ) )
			: 'https://lowcode.opasuite.com.br';

		$output['token'] = ! empty( $input['token'] )
			? sanitize_text_field( trim( $input['token'] ) )
			: '';

		$output['permitir_login_anonimo'] = ! empty( $input['permitir_login_anonimo'] )
			? sanitize_text_field( trim( $input['permitir_login_anonimo'] ) )
			: 'off';

		$output['facebook_appid'] = ! empty( $input['facebook_appid'] )
			? sanitize_text_field( trim( $input['facebook_appid'] ) )
			: '';

		$output['google_credential'] = ! empty( $input['google_credential'] )
			? sanitize_text_field( trim( $input['google_credential'] ) )
			: '';

		$output['google_oauth'] = ! empty( $input['google_oauth'] )
			? sanitize_text_field( trim( $input['google_oauth'] ) )
			: '';

		$allowed_modes = array( 'all', 'selected', 'except', 'front_page' );
		$output['pages_mode'] = isset( $input['pages_mode'] ) && in_array( $input['pages_mode'], $allowed_modes, true )
			? $input['pages_mode']
			: 'all';

		$output['selected_pages'] = array();
		if ( ! empty( $input['selected_pages'] ) && is_array( $input['selected_pages'] ) ) {
			$output['selected_pages'] = array_map( 'absint', $input['selected_pages'] );
		}

		return $output;
	}

	/**
	 * Render settings page UI
	 */
	public function render_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$options = OpaSuite_Header_Script::get_options();

		// Fetch published pages and posts for selection
		$pages = get_pages( array(
			'post_status' => 'publish',
			'number'      => 500,
			'sort_column' => 'post_title',
			'sort_order'  => 'ASC',
		) );

		$posts = get_posts( array(
			'post_type'   => 'post',
			'post_status' => 'publish',
			'numberposts' => 200,
			'orderby'     => 'title',
			'order'       => 'ASC',
		) );

		?>
		<div class="wrap opasuite-admin-wrap">
			<div class="opasuite-header-banner">
				<div class="opasuite-logo">
					<span class="dashicons dashicons-code-standards"></span>
					<h2>Opa! Suite - Webchat</h2>
				</div>
				<p>Configure a inserção automática do código JavaScript do OpaSuite no cabeçalho das páginas do seu WordPress.</p>
			</div>

			<?php settings_errors(); ?>

			<form method="post" action="options.php" id="opasuite-settings-form">
				<?php
				settings_fields( 'opasuite_options_group' );
				?>

				<div class="opasuite-tabs-nav">
					<button type="button" class="opasuite-tab-btn active" data-tab="tab-general">
						<span class="dashicons dashicons-admin-generic"></span> Configurações Principais
					</button>
					<button type="button" class="opasuite-tab-btn" data-tab="tab-params">
						<span class="dashicons dashicons-admin-network"></span> Parâmetros do Script
					</button>
					<button type="button" class="opasuite-tab-btn" data-tab="tab-pages">
						<span class="dashicons dashicons-admin-page"></span> Seleção de Páginas
					</button>
					<button type="button" class="opasuite-tab-btn" data-tab="tab-preview">
						<span class="dashicons dashicons-visibility"></span> Pré-visualização do Código
					</button>
				</div>

				<!-- TAB 1: General Settings -->
				<div class="opasuite-tab-content active" id="tab-general">
					<div class="opasuite-card">
						<h3>Status do Script</h3>
						<table class="form-table">
							<tr>
								<th scope="row">Ativar Injeção de Script</th>
								<td>
									<label class="opasuite-switch">
										<input type="checkbox" name="<?php echo esc_attr( $this->option_key ); ?>[enabled]" value="1" <?php checked( $options['enabled'], '1' ); ?>>
										<span class="opasuite-slider round"></span>
									</label>
									<span class="description">Marque para ativar a inclusão do script no cabeçalho (wp_head).</span>
								</td>
							</tr>
						</table>

						<h3>Conexão & Credenciais</h3>
						<table class="form-table">
							<tr>
								<th scope="row"><label for="opasuite_domain">Domínio do OpaSuite *</label></th>
								<td>
									<input type="url" id="opasuite_domain" name="<?php echo esc_attr( $this->option_key ); ?>[domain]" value="<?php echo esc_attr( $options['domain'] ); ?>" class="regular-text" required placeholder="https://lowcode.opasuite.com.br">
									<p class="description">Endereço base do serviço (ex: <code>https://lowcode.opasuite.com.br</code>).</p>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="opasuite_token">Token / Identificador *</label></th>
								<td>
									<input type="text" id="opasuite_token" name="<?php echo esc_attr( $this->option_key ); ?>[token]" value="<?php echo esc_attr( $options['token'] ); ?>" class="regular-text" required placeholder="69c27ed98f4ad77c46cd4634">
									<p class="description">Identificador/Token da sua aplicação no OpaSuite (ex: <code>69c27ed98f4ad77c46cd4634</code>).</p>
								</td>
							</tr>
						</table>
					</div>
				</div>

				<!-- TAB 2: Script Parameters -->
				<div class="opasuite-tab-content" id="tab-params">
					<div class="opasuite-card">
						<h3>Parâmetros de Inicialização (opa.init)</h3>
						<p class="description" style="margin-bottom: 20px;">Estes valores serão formatados no objeto JSON repassado ao inicializador do OpaSuite no cabeçalho.</p>
						
						<table class="form-table">
							<tr>
								<th scope="row"><label for="opasuite_permitir_login_anonimo">Permitir Login Anônimo</label></th>
								<td>
									<select id="opasuite_permitir_login_anonimo" name="<?php echo esc_attr( $this->option_key ); ?>[permitir_login_anonimo]">
										<option value="on" <?php selected( $options['permitir_login_anonimo'], 'on' ); ?>>on (Ativado)</option>
										<option value="off" <?php selected( $options['permitir_login_anonimo'], 'off' ); ?>>off (Desativado)</option>
									</select>
									<p class="description">Define o parâmetro <code>permitir_login_anonimo</code> no JSON.</p>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="opasuite_facebook_appid">Facebook App ID</label></th>
								<td>
									<input type="text" id="opasuite_facebook_appid" name="<?php echo esc_attr( $this->option_key ); ?>[facebook_appid]" value="<?php echo esc_attr( $options['facebook_appid'] ); ?>" class="regular-text" placeholder="ID do app do Facebook (opcional)">
									<p class="description">Chave <code>facebook_appid</code>.</p>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="opasuite_google_credential">Google Credential</label></th>
								<td>
									<input type="text" id="opasuite_google_credential" name="<?php echo esc_attr( $this->option_key ); ?>[google_credential]" value="<?php echo esc_attr( $options['google_credential'] ); ?>" class="regular-text" placeholder="Google Credential (opcional)">
									<p class="description">Chave <code>google_credential</code>.</p>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="opasuite_google_oauth">Google OAuth</label></th>
								<td>
									<input type="text" id="opasuite_google_oauth" name="<?php echo esc_attr( $this->option_key ); ?>[google_oauth]" value="<?php echo esc_attr( $options['google_oauth'] ); ?>" class="regular-text" placeholder="Google OAuth Client ID (opcional)">
									<p class="description">Chave <code>google_oauth</code>.</p>
								</td>
							</tr>
						</table>
					</div>
				</div>

				<!-- TAB 3: Page Selection -->
				<div class="opasuite-tab-content" id="tab-pages">
					<div class="opasuite-card">
						<h3>Regra de Exibição das Páginas</h3>
						<table class="form-table">
							<tr>
								<th scope="row">Onde inserir o script?</th>
								<td>
									<fieldset>
										<label style="display:block; margin-bottom: 8px;">
											<input type="radio" name="<?php echo esc_attr( $this->option_key ); ?>[pages_mode]" value="all" <?php checked( $options['pages_mode'], 'all' ); ?>>
											<strong>Em todas as páginas</strong> (Todo o site front-end)
										</label>
										<label style="display:block; margin-bottom: 8px;">
											<input type="radio" name="<?php echo esc_attr( $this->option_key ); ?>[pages_mode]" value="front_page" <?php checked( $options['pages_mode'], 'front_page' ); ?>>
											<strong>Apenas na Página Inicial</strong> (Homepage)
										</label>
										<label style="display:block; margin-bottom: 8px;">
											<input type="radio" name="<?php echo esc_attr( $this->option_key ); ?>[pages_mode]" value="selected" <?php checked( $options['pages_mode'], 'selected' ); ?>>
											<strong>Apenas nas páginas selecionadas abaixo</strong>
										</label>
										<label style="display:block; margin-bottom: 8px;">
											<input type="radio" name="<?php echo esc_attr( $this->option_key ); ?>[pages_mode]" value="except" <?php checked( $options['pages_mode'], 'except' ); ?>>
											<strong>Em todas as páginas, EXCETO nas selecionadas abaixo</strong>
										</label>
									</fieldset>
								</td>
							</tr>
						</table>

						<div class="opasuite-pages-selector-container">
							<div class="opasuite-list-header">
								<h4>Selecione as Páginas e Posts:</h4>
								<div class="opasuite-list-actions">
									<input type="text" id="opasuite-search-pages" placeholder="🔍 Filtrar por título..." class="regular-text">
									<button type="button" class="button button-secondary" id="opasuite-select-all">Selecionar Todos</button>
									<button type="button" class="button button-secondary" id="opasuite-deselect-all">Desmarcar Todos</button>
								</div>
							</div>

							<div class="opasuite-pages-box">
								<h5 class="opasuite-section-title">Páginas (<?php echo count( $pages ); ?>)</h5>
								<ul class="opasuite-checkbox-list">
									<?php if ( ! empty( $pages ) ) : ?>
										<?php foreach ( $pages as $p ) : ?>
											<li class="page-item">
												<label>
													<input type="checkbox" name="<?php echo esc_attr( $this->option_key ); ?>[selected_pages][]" value="<?php echo esc_attr( $p->ID ); ?>" <?php checked( in_array( $p->ID, (array) $options['selected_pages'], true ) ); ?>>
													<span class="item-title"><?php echo esc_html( $p->post_title ); ?></span>
													<span class="item-id">(ID: <?php echo esc_html( $p->ID ); ?>)</span>
												</label>
											</li>
										<?php endforeach; ?>
									<?php else : ?>
										<li><em>Nenhuma página publicada encontrada.</em></li>
									<?php endif; ?>
								</ul>

								<?php if ( ! empty( $posts ) ) : ?>
									<h5 class="opasuite-section-title" style="margin-top: 20px;">Posts do Blog (<?php echo count( $posts ); ?>)</h5>
									<ul class="opasuite-checkbox-list">
										<?php foreach ( $posts as $post ) : ?>
											<li class="page-item">
												<label>
													<input type="checkbox" name="<?php echo esc_attr( $this->option_key ); ?>[selected_pages][]" value="<?php echo esc_attr( $post->ID ); ?>" <?php checked( in_array( $post->ID, (array) $options['selected_pages'], true ) ); ?>>
													<span class="item-title"><?php echo esc_html( $post->post_title ); ?></span>
													<span class="item-id">(ID: <?php echo esc_html( $post->ID ); ?>)</span>
												</label>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>

				<!-- TAB 4: Code Preview -->
				<div class="opasuite-tab-content" id="tab-preview">
					<div class="opasuite-card">
						<h3>Código JavaScript Injetado no Header</h3>
						<p class="description">Este é o formato exato do código que será inserido dentro da tag <code>&lt;head&gt;</code> do site com base nas suas configurações:</p>
						
						<div class="opasuite-code-preview-box">
							<button type="button" class="button button-secondary opasuite-copy-btn" id="opasuite-copy-code">
								<span class="dashicons dashicons-admin-page"></span> Copiar Código
							</button>
							<pre><code id="opasuite-preview-output"></code></pre>
						</div>
					</div>
				</div>

				<div class="opasuite-submit-bar">
					<?php submit_button( 'Salvar Configurações', 'primary large', 'submit', false ); ?>
				</div>
			</form>
		</div>
		<?php
	}
}
