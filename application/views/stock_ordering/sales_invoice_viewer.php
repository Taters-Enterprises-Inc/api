
<style>
	#contract_paper{
		font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
	}
	#contract_paper th{
		font-size: 16px;
	}
	#contract_paper table,#contract_paper td {
		font-size: 14px;
		color: black;
	}

	#contract_paper td, #contract_paper th{
		padding: 4px 10px 4px 10px;
	}
	
	/* #contract_paper #title_wrapper {
		display: flex;
		justify-content: center;
		align-items: center;
		flex-direction: column;
		color: black;
	} */
	
	#contract_paper #title_wrapper {
		text-align: center;
	}
	#contract_paper #title_wrapper img{
		margin: 10px 0 10px 0;
	}
	#contract_paper #title_wrapper h1{
		font-weight: bold;
		font-size: 20px;
		color: black;
	}

	#text_design {
		text-align: center;
	}

	.custom-span {
		font-size: 1rem;
		font-weight: normal;
		line-height: 1.5;
	}

	.page_break { page-break-before: always; }

</style>

<section id="contract_paper" style="margin-bottom: 10px;">
	<div id="title_wrapper" style="margin-bottom: 30px;">
		<img src="assets/images/shared/logo/contract_logo.png" width="250">
	</div>
	<table style="width:100%">
		<tr>
			<th style="text-align: left">SOLD TO: <span class="custom-span"><?php echo $store_details->name; ?></span></th>
		</tr>
	</table>
</section>

<section id="contract_paper" style="margin-bottom: 10px;">
	<table style="width:100%">
		<tr>
			<th style="text-align: left">ADDRESS: <span class="custom-span"><?php echo $store_details->address; ?></span></th>
		</tr>
	</table>
</section>

<section id="contract_paper" style="margin-bottom: 10px;">
	<table style="width:100%">
		<tr>
			<th style="text-align: left">TIN:</th>
		</tr>
	</table>
</section>

<section id="contract_paper" style="margin-bottom: 50px;">
	<table style="width:100%">
		<tr>
			<th style="text-align: left">BUSINESS STYLE:</th>
		</tr>
	</table>
</section>

<section id="contract_paper" style="margin-bottom: 10px;">
	<table style="width:100%">
		<tr style="background-color: black; color: white;">
			<th style="text-align: left">ITEM NAME</th>
			<th style="text-align: left">UOM</th>
			<th style="text-align: left">QUANTITY</th>
			<th style="text-align: left">RATE</th>
			<th style="text-align: left">AMOUNT</th>
		</tr>
		<?php
		foreach ($products as $product) {
			?>
			<tr>
				<td><?php echo $product['product_name']; ?></td>
				<td><?php echo $product['uom']; ?></td>
				<td><?php echo $product['delivered_qty']; ?></td>
				<td><?php echo number_format($product['product_rate']); ?></td>
				<td><?php echo number_format($product['total_cost']); ?></td>
			</tr>
			<?php
		}
		?>
	</table>
</section>

<section id="contract_paper" style="margin-top: 180px;">
	<table style="width:100%">
		<tr>
			<th style="text-align: left">SHIP TO:</th>
			<th style="text-align: left">VATABLE SALES: <span class="custom-span"><?php echo number_format($si_details->vatable_sales); ?></span></th>
			<th style="text-align: left">TOTAL SALES: <span class="custom-span"><?php echo number_format($si_details->total_sales); ?></span></th>
		</tr>
		<tr>
			<td><?php echo $store_details->ship_to_address; ?></td>
			<td>&nbsp;</td>
			<td style="font-size: 16px; font-weight: bold;">LESS 12% VAT: <span class="custom-span"><?php echo number_format($si_details->less_vat); ?></span></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td style="text-align: left; font-size: 16px; font-weight: bold;">VAT EXEMPT SALE: <span class="custom-span"><?php echo $si_details->vat_exempt_sales; ?></span></td>
			<td style="text-align: left; font-size: 16px; font-weight: bold;">AMOUNT (VAT EX): <span class="custom-span"><?php echo number_format($si_details->vat_ex_amount); ?></span></td>
		</tr>
		<tr>
			<td style="font-size: 16px; font-weight: bold;">REMARKS:</td>
			<td>&nbsp;</td>
			<td style="text-align: left; font-size: 16px; font-weight: bold;">LESS SC/PWD: <span class="custom-span"><?php echo $si_details->less_sc_pwd; ?></span></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td style="text-align: left; font-size: 16px; font-weight: bold;">ZERO RATE SALES: <span class="custom-span"><?php echo $si_details->zero_rated_sales; ?></span></td>
			<td style="text-align: left; font-size: 16px; font-weight: bold;">AMOUNT DUE: <span class="custom-span"><?php echo number_format($si_details->amount_due); ?></span></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td style="text-align: left; font-size: 16px; font-weight: bold;">ADD 12% VAT: <span class="custom-span"><?php echo number_format($si_details->add_vat); ?></span></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td style="text-align: left; font-size: 16px; font-weight: bold;">VAT AMOUNT: <span class="custom-span"><?php echo number_format($si_details->vat_amount); ?></span></td>
			<td style="text-align: left; font-size: 16px; font-weight: bold;">TOTAL AMOUNT DUE: <span class="custom-span"><?php echo number_format($si_details->total_amount_due); ?></span></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td style="text-align: left; font-size: 16px; font-weight: bold;">By:</td>
		</tr>
	</table>
</section>