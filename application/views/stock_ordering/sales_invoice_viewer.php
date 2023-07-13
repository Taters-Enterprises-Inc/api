
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
		border: 1px solid black;
		border-collapse: collapse;
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

	.page_break { page-break-before: always; }

</style>

<section id="contract_paper" style="margin-bottom: 10px;">
	<div id="title_wrapper">
		<img src="assets/images/shared/logo/contract_logo.png" width="250">
		<h1>
			<?php echo $store_name; ?>
		</h1>
	</div>
	<table style="width:100%">
		<tr style="background-color: black; color: white; text-align: center;">
			<th colspan="4">THEORETICAL SALES INVOICE</th>
		</tr>
		<tr>
			<th style="text-align: left">Product ID</th>
			<th style="text-align: left">Product Name</th>
			<th style="text-align: left">UOM</th>
			<th style="text-align: left">Final Product Cost</th>
		</tr>
		<?php
		foreach ($products as $product) {
			?>
			<tr>
				<td><?php echo $product['product_id']; ?></td>
				<td><?php echo $product['product_name']; ?></td>
				<td><?php echo $product['uom']; ?></td>
				<td><?php echo $product['total_cost']; ?></td>
			</tr>
			<?php
		}
		?>
	</table>
</section>

<h3>Sum (Theoretical): 349220</h3>