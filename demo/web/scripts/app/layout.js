dojo.provide('app.layout');

app.layout = (function(){
    /**
     * Setup Flash
     * 
     * @param {DOMNode} node
     */
    var _setupFlash = function(node){
        dojo.require('dojo.fx');
        dojo.connect(node, 'onclick', function(){
            dojo.fx.wipeOut({
                node: this
            }).play();
        });
    };
    
    return {
        /**
         * Init
         * 
         * @param {Object} args
         */
        init: function(args){
            dojo.addOnLoad(function(){
                _setupFlash(dojo.byId('flash-container'));  
            });
        }
    };
})();