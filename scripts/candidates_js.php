<script>
	function generate_code(candidate_id,elm_return,updating_table){
		candidate_id = candidate_id || '';
		updating_table = updating_table || 'true';
		$.fancybox.open({ href: "candidate_generator_code.php?candidate_id="+candidate_id+"&elm_return="+elm_return+"&updating_table="+updating_table, width: "350",height: "80%", type: "iframe" });
	}
</script>