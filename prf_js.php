
<script>
	function load_checker(cost_center_code,nominal){
		cost_center_code = cost_center_code || "";
		if(cost_center_code != ""){
			nominal = nominal || 0;
			$.get( "ajax/prf_ajax.php?mode=get_select_checker&cost_center_code="+cost_center_code, function(data) {
				$("#checker_by").val(data);
			});
			$.get( "ajax/prf_ajax.php?mode=get_select_signer&cost_center_code="+cost_center_code, function(data) {
				$("#signer_by").val(data);
			});
			if(nominal > 0){
				$.get( "ajax/prf_ajax.php?mode=get_select_approve&cost_center_code="+cost_center_code+"&nominal="+nominal, function(data) {
					$("#approve_by").val(data);
				});
			}
		}
	}
</script>