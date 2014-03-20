
window.session_stream_data = window.session_stream_data || {};

( function($){
    
    var comments;

    var ajax_action = 'session-stream-comments';

    var comments = session_stream_data.comments = session_stream_data.comments || {};
    var GC  = {}; // Gelato Comments
    // _.extend( comments, { model: {}, view: {}, routes: {}, router: {}, template: session_stream_data.comments.template });
   
    GC.Comment = Backbone.Model.extend({});

    GC.Comments = Backbone.Collection.extend({
        
        model: Comment,

    });

    var commentsCollection = new GC.Comments( session_stream_data.comments );
    
   
    var CommentView = Backbone.View.extend({
        
        // tagName:  "li",
        // Cache the template function for a single item.
        template: _.template( $('#comment-template').html() ),
        // The DOM events specific to an item.
        events: {
          "click li"   : "highlight",
        },

        initialize: function() {
            // where all the items are contained
            this.$shell = $('#comments .commentlist'); 

            // Later we'll look at:
            // this.listenTo(someCollection, 'all', this.render);
            // but you can actually run this example right now by
            // calling todoView.render();
        },
        render: function() {
            this.$shell.prepend( this.template( this.model.toJSON() ) )
        },

        highlight: function( ) {
             this.$el.toggleClass('hightlight');
        }
    });

    /*
    // this is is where 
    var CommentsView = Backbone.View.extend({
        
        el: "#comments",
        
        form: $("#commentform"),

        events: {
            'submit #commentform': 'addComment',
           
        },

        initialize: function() {
            
            this.listenTo( commentsCollection, 'add', this.addOne );
            this.listenTo( commentsCollection, 'reset', this.addAll );
            this.listenTo( commentsCollection, 'all', this.render );
            
        },
        add: function(){
           
            console.log( this );

        },
        addAll: function(){
           
            console.log( this );

        },
        render: function( ) {
            // render all the comments
            console.log( 're render all the comments' );

        },
        editComment: function(  ) {

        },
        addComment: function( e ) {

            // prevent the form from submitting
            e.preventDefault(); 
            
            var form_data = {
                action : ajax_action,
                make: 'new',
                form: this.form.serialize()
            }

            $.ajax( {
                url: session_stream_data.meta.ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: form_data,
                success: function( json ) {

                    commentsCollection.add( json.comment );
                    // comments.collection.add( json.comment );
                    console.log( json );
                }
            });
        }

    

    });

    var Form = new CommentsView; // start the app.
    // var CommentItem = new CommentView;
     */
   
    $(function() {

      console.log('on page load ');

        var firstComment = new Comment( comments[0] );
       
        console.log( firstComment.toJSON() , firstComment.get('ID') );
        var commentView = new CommentView( { model: firstComment });
        commentView.render();
        commentsCollection.set( comments );
      /*
      var continentsCollection = new ContinentsCollection();
      continentsCollection.reset([{name: "Asia"}, {name: "Africa"}]);
      // initialize the view and pass the collection
      var continentsView = new ContinentsView({collection: continentsCollection});
      */
    });
   
}( jQuery ));
