
function changeOrdering( param )
{
	var form = document.getElementById('jForm');
	form.filter_order.value = param;
	form.submit();
}

function swapImage(img_preview_url){
	document.getElementById('img_preview').src = img_preview_url;
}