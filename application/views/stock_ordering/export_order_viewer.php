
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
</section>

<section id="contract_paper" style="margin-bottom: 10px;">
	<table style="width:100%">
		<tr>
			<th style="text-align: right">Requested Delivery Date: <span class="custom-span"><?php echo $store_details->requested_delivery_date; ?></span></th>
		</tr>
	</table>
</section>

<section id="contract_paper" style="margin-bottom: 10px;">
	<table style="width:100%">
		<tr>
			<th>&nbsp;</th>
		</tr>
	</table>
</section>

<section id="contract_paper" style="margin-bottom: 10px;">
	<table style="width:100%">
		<tr>
			<th style="text-align: left">Order ID: <span class="custom-span"><?php echo $store_details->id; ?></span></th>
		</tr>
	</table>
</section>

<section id="contract_paper" style="margin-bottom: 10px;">
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

<section id="contract_paper" style="margin-bottom: 20px;">
	<table style="width:100%">
		<tr>
			<th style="text-align: left">REMARKS:</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tr>

		<?php

		foreach ($remarks as $remark) {
			?>
			<tr>
				<td><?php echo $remark['remarks']; ?></td>
				<td><?php echo $remark['first_name'] . " " . $remark['last_name']; ?></td>
				<td><?php echo $remark['date']; ?></td>
			</tr>
			<?php
		}
		?>

	</table>
</section>


<section id="contract_paper" style="margin-bottom: 10px;">
	<table style="width:100%">
		<tr style="background-color: black; color: white;">
			<th style="text-align: left">PRODUCT ID</th>
			<th style="text-align: left">ITEM NAME</th>
			<th style="text-align: left">UOM</th>
			<th style="text-align: left">QUANTITY</th>
		</tr>

		<?php

		$totalCost = 0; 

		foreach ($products as $product) {
			?>
			<tr>
				<td><?php echo $product['product_id']; ?></td>
				<td><?php echo $product['product_name']; ?></td>
				<td><?php echo $product['uom']; ?></td>
				<td><?php echo $product['order_qty']; ?></td>
			</tr>

		<?php

			$totalCost += $product['cost'];

		}
		?>
		<tr >
			<td colspan="4" style="background-color: black; text-align: left; color: white">Total</td>
			<td style="text-align: left"><?php echo number_format($totalCost); ?></td>
		</tr>
	</table>
</section>