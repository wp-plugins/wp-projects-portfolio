
function tinyEvent(ed){

	ed.onClick.add(function(ed, e){

		var content = tinyMCE.activeEditor.getContent();

		if(content.match(/ENTER THE PROJECT REQUIREMENTS HERE/i)){

			ed.setContent('');

			ed.selection.collapse();

		} 

    });

}