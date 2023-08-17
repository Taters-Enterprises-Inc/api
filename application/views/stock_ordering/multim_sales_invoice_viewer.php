
<style>
	#contract_paper{
		font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
	}
	#contract_paper th{
		font-size: 16px;
		border: 1px solid black;
		border-collapse: collapse;
	}
	#contract_paper table,#contract_paper td {
		font-size: 14px;
		color: black;
		border: 1px solid black;
		border-collapse: collapse;
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
</section>

<section id="contract_paper" style="margin-bottom: 10px;">
	<table style="width:100%">
		<tr style="background-color: black; color: white;">
			<th style="text-align: left">Invoice Date</th>
			<th style="text-align: left">SI</th>
			<th style="text-align: left">Store</th>
			<th style="text-align: left">Product Code</th>
			<th style="text-align: left">Product Name</th>
			<th style="text-align: left">UOM</th>
			<th style="text-align: left">Quantity</th>
			<th style="text-align: left">Total</th>
		</tr>
		<?php
		foreach ($multi_m as $data) {
			?>
			<tr>
				<td><?php echo $data['invoice_date']; ?></td>
				<td><?php echo $data['si']; ?></td>
				<td><?php echo $data['store']; ?></td>
				<td><?php echo $data['multim_product_code']; ?></td>
				<td><?php echo $data['multim_product_name']; ?></td>
				<td><?php echo $data['uom']; ?></td>
				<td><?php echo $data['quantity']; ?></td>
				<td><?php echo $data['total']; ?></td>
			</tr>
			<?php
		}
		?>
	</table>
</section>