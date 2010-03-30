
function changeOrdering( param )
{
	var form = document.getElementById('jForm');
	form.filter_order.value = param;
	form.submit();
}

