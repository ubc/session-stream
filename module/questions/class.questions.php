<?php
class SCCT_Module_Questions extends Session_CCT_Module {
	
	function __construct() {
		parent::__construct( "Questions" );
		
    	wp_register_style(  'scct-view-questions', SESSION_CCT_DIR_URL.'/module/questions/view-questions.css' );
    	wp_register_script( 'scct-view-questions', SESSION_CCT_DIR_URL.'/module/questions/view-questions.js', array( 'jquery' ), '1.0', true );
    	wp_register_script( 'popcornjs-questions', SESSION_CCT_DIR_URL.'/module/questions/popcorn.question.js', array( 'jquery', 'popcornjs' ), '1.0', true );
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
					<option value="skip" <?php selected( $questions['meta']['mode'] == 'skip' ); ?>>Skippable</option>
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
			<ul>
				<?php
					$this->admin_answer( $answers[0] );
					$this->admin_answer( $answers[1] );
					$this->admin_answer( $answers[2] );
					$this->admin_answer( $answers[3] );
				?>
			</ul>
		</div>
		<?php
	}
	
	public function admin_answer( $data = array() ) {
		?>
		<li>
			<input type="text" name="<?php $this->field_name( array( "list", "", "answer_title" ) ); ?>" value="<?php echo $data['title']; ?>" />
			<label>
				<input type="checkbox" name="<?php $this->field_name( array( "list", "", "answer_correct" ) ); ?>" <?php checked( $data['correct'] == "on" ); ?> />
				This answer is correct.
			</label>
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
	
	public function question_template() {
		ob_start();
		?>
		<div class="question-dialog">
			<div class="question">
				{{=it.title}}
			</div>
			<ul class="answers">
				{{~it.answers :value:index}}
					<li class="answer">
						<label>
							<input name="answer" type="radio" />
							{{=value.title}}!
						</label>
					</li>
				{{~}}
			</ul>
			<button class="btn btn-inverse" onclick="SCCT_Module_Questions.submit();">Submit</button>
			<button class="btn btn-primary" onclick="SCCT_Module_Questions.skip();">Skip</button>
		</div>
		<?php
		return ob_get_clean();
	}
	
	function localize_view( $data ) {
		$data['questions'] = $this->data();
		$data['questions']['template'] = $this->question_template();
		
		foreach ( $data['questions']['list'] as $index => $question ) {
			$data['questions']['list'][$index]['synctime'] = Session_CCT_View::string_to_seconds( $question['time'] );
		}
		
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
}

new SCCT_Module_Questions();