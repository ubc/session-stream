<?php
class SCCT_Module_Questions extends Session_CCT_Module {
	
	function __construct() {
		parent::__construct( "Questions" );
		
    	wp_register_style(  'scct-view-questions', SESSION_CCT_DIR_URL.'/module/questions/view-questions.css' );
    	wp_register_script( 'scct-view-questions', SESSION_CCT_DIR_URL.'/module/questions/view-questions.js', array( 'jquery' ), '1.0', true );
    	wp_register_script( 'popcornjs-questions', SESSION_CCT_DIR_URL.'/module/questions/popcorn.question.js', array( 'jquery', 'popcornjs' ), '1.0', true );
		
		add_action( 'wp_ajax_scct_answer', array( $this, 'register_answer' ) );
		add_action( 'wp_ajax_nopriv_scct_answer', array( $this, 'register_answer' ) );
	}
	
	public function load_admin() {
		add_filter( 'scct_localize_admin', array( $this, 'localize_admin' ) );
	}
	
	public function load_view() {
		add_filter( 'scct_localize_view', array( $this, 'localize_view' ) );
	}
	
	public function admin( $post, $box ) {
		$questions = get_post_meta( $post->ID, 'session_cct_questions', true );
		
		?>
		<div class="scct-admin-section">
			<label>
				Mode
				<select name="<?php $this->field_name( array( "meta", "mode" ) ); ?>">
					<option value="skippable" <?php selected( $questions['meta']['mode'] == 'skippable' ); ?>>Skippable</option>
					<option value="any" <?php selected( $questions['meta']['mode'] == 'any' ); ?>>Must Answer</option>
					<option value="correct" <?php selected( $questions['meta']['mode'] == 'correct' ); ?>>Must Answer Correctly</option>
					<option value="disabled" <?php selected( $questions['meta']['mode'] == 'disabled' ); ?>>Disable Module</option>
				</select>
			</label>
			<br />
			<label>
				<input type="checkbox" name="<?php $this->field_name( array( "meta", "random" ) ); ?>" <?php checked( $questions['meta']['random'] ); ?> />
				Randomize Order
			</label>
		</div>
		<div class="scct-question-list scct-section-list">
			<?php
				if ( ! empty( $questions['list'] ) ) {
					foreach ( $questions['list'] as $question ) {
						$this->admin_question( $question );
					}
				}
			?>
		</div>
		<a class="button" onclick="Session_CCT_Admin.addSection( this, 'question' );">Add Question</a>
		<?php
	}
	
	public function admin_question( $data = array() ) {
		$title   = ( empty( $data['title']   ) ? ""      : $data['title'] );
		$time    = ( empty( $data['time']    ) ? "0:00"  : $data['time'] );
		$answers = ( empty( $data['answers'] ) ? array() : $data['answers'] );
		
		?>
		<div class="scct-question scct-admin-section">
			<span class="scct-section-meta">
				<a class="scct-close" onclick="Session_CCT_Admin.removeSection( this );">
					&#10006;
				</a>
				<a class="scct-up" onclick="Session_CCT_Admin.move( this, false );">
					<img src="<?php echo SESSION_CCT_DIR_URL; ?>/img/arrow-down.png" />
				</a>
				<a class="scct-down" onclick="Session_CCT_Admin.move( this, true );">
					<img src="<?php echo SESSION_CCT_DIR_URL; ?>/img/arrow-up.png" />
				</a>
			</span>
			<label>
				Title: 
				<input type="text" name="<?php $this->field_name( array( "list", "", "title" ) ); ?>" value="<?php echo $title; ?>" />
			</label>
			<label>
				Time: 
				<input type="text" name="<?php $this->field_name( array( "list", "", "time" ) ); ?>" value="<?php echo $time; ?>" />
			</label>
			<br />
			Answers
			<ul class="scct-section-list">
				<?php
					if ( empty( $answers ) ) {
						$this->admin_answer();
						$this->admin_answer();
						$this->admin_answer();
					} else {
						foreach ( $answers as $index => $answer ) {
							$this->admin_answer( $answer );
						}
					}
				?>
			</ul>
			<a class="button" onclick="Session_CCT_Admin.addSection( this, 'answer' );">Add Answer</a>
		</div>
		<?php
	}
	
	public function admin_answer( $data = array() ) {
		?>
		<li class="scct-answer scct-admin-section no-css">
			<input type="text" name="<?php $this->field_name( array( "list", "", "answer_title" ) ); ?>" value="<?php echo $data['title']; ?>" />
			<label>
				<input type="checkbox" name="<?php $this->field_name( array( "list", "", "answer_correct" ) ); ?>" <?php checked( $data['correct'] == "on" ); ?> />
				This answer is correct.
			</label>
			<span class="scct-section-meta no-float">
				<a class="scct-close" onclick="Session_CCT_Admin.removeSection( this );">
					&#10006;
				</a>
			</span>
		</li>
		<?php
	}
	
	function localize_admin( $data ) {
		ob_start();
		$this->admin_question();
		$data['template']['question'] = ob_get_clean();
		ob_start();
		$this->admin_answer();
		$data['template']['answer'] = ob_get_clean();
		return $data;
	}
	
	public function view() {
		wp_enqueue_script( 'popcornjs-questions' );
		wp_enqueue_script( 'scct-view-questions' );
		wp_enqueue_style(  'scct-view-questions' );
		
		?>
		<div id="scct-questions"></div>
		<?php
	}
	
	public function question_template( $data ) {
		ob_start();
		?>
		<div class="question-dialog question-{{=it.index}} unanswered" data-id="{{=it.index}}">
			<div class="error" style="display: none;">
				Incorrect Answer
				<br /><br />
			</div>
			<div class="question">
				{{=it.title}}
			</div>
			<ul class="answers">
				{{~it.answers :value:index}}
					<li class="answer">
						<label>
							<div>
								<input class="scctq-answer" name="answer" type="radio" value="{{=value.index}}" />
								{{=value.title}}
							</div>
						</label>
					</li>
				{{~}}
			</ul>
			<button class="btn btn-inverse button submit" onclick="SCCT_Module_Questions.skip(this);">Okay</button>
			<?php if ( $data['meta']['mode'] == 'skippable' ): ?>
				<button class="btn btn-primary button" onclick="SCCT_Module_Questions.skip(this);">Skip</button>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}
	
	function localize_view( $data ) {
		$data['questions'] = $this->data();
		$data['questions']['template'] = $this->question_template( $data['questions'] );
		
		foreach ( $data['questions']['list'] as $index => $question ) {
			foreach ( $question['answers'] as $answer_index => $answer ) {
				$answer['index'] = $answer_index;
				$question['answers'][$answer_index] = $answer;
			}
			
			$question['index'] = $index;
			$question['synctime'] = Session_CCT_View::string_to_seconds( $question['time'] );
			$data['questions']['list'][$index] = $question;
		}
		
		error_log( "Questions ".print_r( $data['questions'], TRUE ) );
		
		return $data;
	}
	
	public function save( $post_id ) {
		$question = null;
		$answer = null;
		$list = array();
		foreach ( $_POST[$this->slug]['list'] as $field ) {
			reset( $field );
			$key = key( $field );
			$value = $field[$key];
			
			if ( Session_CCT_Admin::starts_with( $key, 'answer' ) ) {
				$key = substr( $key, 7 );
				
				if ( $key == 'title' ) {
					if ( ! empty( $answer ) ) {
						$question['answers'][] = $answer;
					}
					
					$answer = array();
				}
				
				$answer[$key] = $value;
			} else {
				if ( $key == 'title' ) {
					if ( ! empty( $question ) ) {
						if ( ! empty( $answer ) ) {
							$question['answers'][] = $answer;
						}
						
						$list[] = $question;
						$answer = null;
					}
					
					$question = array();
				}
				
				$question[$key] = $value;
			}
		}
		
		$question['answers'][] = $answer;
		$list[] = $question;
		$_POST[$this->slug]['list'] = $list;
		$_POST[$this->slug]['meta']['random'] = $_POST[$this->slug]['meta']['random'] == "on";
		
		parent::save( $post_id );
	}
	
	public function register_answer() {
		$data = $this->data( $_POST['session_id'] );
		$data = $data['list'][$_POST['question']];
		echo $data['answers'][$_POST['answer']]['correct'] == "on";
		die();
	}
}

new SCCT_Module_Questions();