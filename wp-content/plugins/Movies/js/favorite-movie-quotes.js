const { registerBlockType } = wp.blocks;

registerBlockType( 'movies/favorite-movie-quotes', {

    title: "Favorite Movie Quotes",
    description: "Your favorite movie quote.",
    category: "layout",
    icon: "format-quote",
    
    attributes: {
		quote: { "type": "string",  "default": "Insert fav. movie quote" }
    },

    edit: function (props) {
        return ( 
            wp.element.createElement('input', 
                {   type: 'text', 
                    name:"quote", 
                    value: props.attributes.quote, 
                    onChange: function (event) {
                        props.setAttributes({quote: event.target.value});
                    }
                }
            )
        );
    },
    save: function () {return null;}
});