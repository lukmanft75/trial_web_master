<script>
	function immediate_duedate(){
		if(document.getElementById("due_date").readOnly == true){
			document.getElementById("due_date").value = 30;
			document.getElementById("due_date").readOnly = false;
		} else {
			document.getElementById("due_date").value = 0;
			document.getElementById("due_date").readOnly = true;
		}
	}
	
	function hitung_total(){
		var numrow = document.getElementById("detail_area1").childElementCount;
		var v_reimbursement = 0;
		var v_fee = 0;
		for(xx=0 ; xx < numrow; xx++){
			if(document.getElementById("after_tax_rate["+ xx +"]").checked == false){
				v_reimbursement = v_reimbursement + (document.getElementById("reimbursement["+ xx +"]").value * 1);
				v_fee = v_fee + (document.getElementById("fee["+ xx +"]").value * 1);
			}
		}
		
		var v_vat = 0;
		var v_is_vat_1 = document.getElementById("is_vat_1").checked;
		var v_is_vat_2 = document.getElementById("is_vat_2").checked;
		if(v_is_vat_1 && v_is_vat_2){ v_vat = (v_reimbursement + v_fee) * 0.1; }
		else if(v_is_vat_1){ v_vat = v_reimbursement * 0.1; }
		else if(v_is_vat_2){ v_vat = v_fee * 0.1; }
		
		var v_tax23 = 0;
		var v_is_tax23_1 = document.getElementById("is_tax23_1").checked;
		var v_is_tax23_2 = document.getElementById("is_tax23_2").checked;
		if(v_is_tax23_1 && v_is_tax23_2){ v_tax23 = (v_reimbursement + v_fee) * -0.02; }
		else if(v_is_tax23_1){ v_tax23 = v_reimbursement * -0.02; }
		else if(v_is_tax23_2){ v_tax23 = v_fee * -0.02; }
		
		var TOTAL = v_reimbursement + v_fee + v_vat + v_tax23;
		
		for(xx=0 ; xx < numrow; xx++){
			if(document.getElementById("after_tax_rate["+ xx +"]").checked === true){
				v_reimbursement = (document.getElementById("reimbursement["+ xx +"]").value * 1);
				v_fee = (document.getElementById("fee["+ xx +"]").value * 1);
				TOTAL = TOTAL + v_reimbursement + v_fee;
			}
		}
		document.getElementById("total").value = formatNumber(TOTAL);
	}
	
	function load_jurnal_details(){
		var numrow = document.getElementById("detail_area1").childElementCount;
		var v_reimbursement = 0;
		var v_fee = 0;
		var nominal = 0;
		var invoice_settings = new Array();
		for(xx=0 ; xx < numrow; xx++){
			if(document.getElementById("after_tax_rate["+ xx +"]").checked == false){
				v_reimbursement = v_reimbursement + (document.getElementById("reimbursement["+ xx +"]").value * 1);
				v_fee = v_fee + (document.getElementById("fee["+ xx +"]").value * 1);
			}
		}
		
		var v_is_vat_1 = document.getElementById("is_vat_1").checked;
		var v_is_vat_2 = document.getElementById("is_vat_2").checked;
		var v_is_tax23_1 = document.getElementById("is_tax23_1").checked;
		var v_is_tax23_2 = document.getElementById("is_tax23_2").checked;
		
		if(v_fee !=0){//dengan reimbursement ataupun tidak
			var vat = 0;
			var tax23 = 0;
			if(v_is_vat_1 && v_is_vat_2){ vat = (v_reimbursement + v_fee)/10; }
			else if(v_is_vat_1){ vat = v_reimbursement/10; }
			else if(v_is_vat_2){ vat = v_fee/10; }
			
			if(v_is_tax23_1 && v_is_tax23_2){ tax23 = (v_reimbursement + v_fee)/50; }
			else if(v_is_tax23_1){ tax23 = v_reimbursement/50; }
			else if(v_is_tax23_2){ tax23 = v_fee/50; }
			
			nominal = v_reimbursement + v_fee + vat - tax23;
			invoice_settings[0] = new Array();
			invoice_settings[0][0] = "<?=$__coa["Piutang Usaha"];?>";
			invoice_settings[0][1] = "Piutang Usaha";
			invoice_settings[0][2] = nominal;
			invoice_settings[0][3] = 0;
			
			nominal = v_reimbursement + v_fee;
			invoice_settings[1] = new Array();
			invoice_settings[1][0] = "";
			invoice_settings[1][1] = "Penjualan";
			invoice_settings[1][2] = 0;
			invoice_settings[1][3] = nominal;
			
			if(v_is_vat_1 && v_is_vat_2){ nominal = (v_reimbursement + v_fee)/10; }
			else if(v_is_vat_1){ nominal = v_reimbursement/10; }
			else if(v_is_vat_2){ nominal = v_fee/10; }
			else nominal = 0;
			if(nominal != 0){
				invoice_settings[2] = new Array();
				invoice_settings[2][0] = "<?=$__coa["Hutang PPN"];?>";
				invoice_settings[2][1] = "Hutang PPN";
				invoice_settings[2][2] = 0;
				invoice_settings[2][3] = nominal;
			}
			
			if(v_is_tax23_1 && v_is_tax23_2){ nominal = (v_reimbursement + v_fee)/50; }
			else if(v_is_tax23_1){ nominal = v_reimbursement/50; }
			else if(v_is_tax23_2){ nominal = v_fee/50; }
			else nominal = 0;
			if(nominal != 0){
				invoice_settings[3] = new Array();
				invoice_settings[3][0] = "<?=$__coa["BDD PPh23"];?>";
				invoice_settings[3][1] = "BDD PPh23";
				invoice_settings[3][2] = nominal;
				invoice_settings[3][3] = 0;
			}
			
		} else if(v_reimbursement != 0 && v_fee == 0){//reimbursement saja
			invoice_settings[0] = new Array();
			invoice_settings[0][0] = "";
			invoice_settings[0][1] = "Piutang Gaji";
			invoice_settings[0][2] = v_reimbursement;
			invoice_settings[0][3] = 0;
			
			invoice_settings[1] = new Array();
			invoice_settings[1][0] = "";
			invoice_settings[1][1] = "Penjualan";
			invoice_settings[1][2] = 0;
			invoice_settings[1][3] = v_reimbursement;
		}
		
		for(xx = 0; xx < 6; xx++){
			substract_row('detail_area','row_detail_');
		}
		document.getElementById("coa[0]").value = "";
		document.getElementById("jurnal_description[0]").value = "";
		document.getElementById("debit[0]").value = "";
		document.getElementById("credit[0]").value = "";
		
		for(xx = 0; xx < invoice_settings.length; xx++){
			adding_row('detail_area','row_detail_');
			try{ document.getElementById("coa["+xx+"]").value = invoice_settings[xx][0]; } catch(e){}
			try{ document.getElementById("jurnal_description["+xx+"]").value = invoice_settings[xx][1]; } catch(e){}
			try{ document.getElementById("debit["+xx+"]").value = invoice_settings[xx][2]; } catch(e){}
			try{ document.getElementById("credit["+xx+"]").value = invoice_settings[xx][3]; } catch(e){}
		}
		substract_row('detail_area','row_detail_');
	}
</script>