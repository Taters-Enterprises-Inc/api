
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
	<?php if($is_download != true) : ?>
	
	#contract_paper #title_wrapper {
		display: flex;
		justify-content: center;
		align-items: center;
		flex-direction: column;
		color: black;
	}

	<?php endif;?>
	
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

	.page_break { page-break-before: always; }

</style>

<section id="contract_paper" style="margin-bottom: 10px;">
	<div id="title_wrapper">
		<img src="<?= $is_download != true ?  base_url() : '' ?>assets/images/shared/logo/contract_logo.png" width="250">
		<br>
		<span style="font-size: 20px;">Operated by: <?= $store_name?></span>
		<br>
		<h1>
			CATERING AND PARTY RESERVATIONS CONTRACT
		</h1>
	</div>
	<table style="width:100%">
		<tr style="background-color: black; color: white; text-align: center;">
			<th colspan="4">FUNCTIONAL DETAILS</th>
		</tr>
		<tr>
			<td >Company Name:</td>	
			<td colspan="3"><?= $company_name?></td>	
		</tr>
		<tr>
			<td >Contact Person:</td>	
			<td><?= $contact_person?></td>	
			<td>Contact Number:</td>	
			<td ><?= $contact_number?></td>	
		</tr>
		<tr>
			<td >Email:</td>	
			<td ><?= $email?></td>	
			<td>Tracking Number:</td>
			<td ><?= $tracking_number?></td>
		</tr>
		<tr>
			<td >Venue:</td>	
			<td colspan="3"><?= $venue?></td>		
		</tr>
		<tr>
			<td >Type of function:</td>	
			<td><?= $type_of_function?></td>	
			<td>No. of Pax:</td>	
			<td><?= $no_of_pax?></td>	
		</tr>
		<tr>
			<td >Date of Event:</td>	
			<td><?= $date_of_event?></td>	
			<td>Event Time:</td>	
			<td><?= $event_date_and_time?></td>	
		</tr>
		<tr>
			<td rowspan="2">Special Arrangements:</td>
			<td rowspan="2" ><?= $special_arrangements?></td>
			<td >Serving Time:</td>
			<td ><?= $serving_time?></td>
		</tr>
		<tr>
			<td>Payment Terms:</td>
			<td ><?= $payment_terms == 'full' ? 'Full Payment (100%)' : 'Partial Payment (50% / 50%)'?></td>
		</tr>
		
		<tr style="background-color: black; color: white; text-align: center;">
			<th colspan="4">PACKAGE SELECTION</th>
		</tr>
		<tr>
			<td style=" font-weight: bold; text-align: center">Quantity</td>	
			<td style=" font-weight: bold; text-align: center">Food Item</td>	
			<td style=" font-weight: bold; text-align: center">Price</td>	
			<td style=" font-weight: bold; text-align: center">Total Cost</td>	
		</tr>
		<?php foreach($package_selection as $package):?>
			<tr>
				<td style=" text-align: center; font-weight: bold; color: red"><?= $package->quantity?></td>	
				<td style="font-weight: bold; text-align: center"><?= $package->name?></td>	
				<td style=" text-align: right"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <?= $package->product_price?></td>	
				<td style=" text-align: right"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <?= $package->calc_price?></td>	
			</tr>

			<?php if(!empty($package->flavors)):?>
			<tr>
				<td ></td>	
				<td style="color: rgb(0, 110, 255); font-weight: bold; text-align: center">Available Flavors:</td>	
				<td ></td>	
				<td ></td>	
			</tr>
			<?php endif;?>

			<?php foreach($package->flavors as $flavor):?>
				<tr>
					<td style="text-align: center; font-weight: bold" ><?= $flavor['quantity']?></td>	
					<td style="color: rgb(0, 110, 255); text-align: center" ><?= $flavor['name']?></td>	
					<td ></td>	
					<td ></td>	
				</tr>
			<?php endforeach;?>
			
		
			<tr>
				<td style="height: <?= $is_download != true ?  '40px' : '18px' ?>;"></td>	
				<td style="height: <?= $is_download != true ?  '40px' : '18px' ?>;"></td>	
				<td style="height: <?= $is_download != true ?  '40px' : '18px' ?>;"></td>	
				<td style="height: <?= $is_download != true ?  '40px' : '18px' ?>;"></td>	
			</tr>
		<?php endforeach;?>
		
		<tr>
			<td style="height: <?= $is_download != true ?  '40px' : '18px' ?>;"></td>	
			<td style="height: <?= $is_download != true ?  '40px' : '18px' ?>;"></td>	
			<td style="height: <?= $is_download != true ?  '40px' : '18px' ?>;"></td>	
			<td style="height: <?= $is_download != true ?  '40px' : '18px' ?>;"></td>	
		</tr>
		
		<tr>
			<td style="height: <?= $is_download != true ?  '40px' : '18px' ?>;"></td>	
			<td style="height: <?= $is_download != true ?  '40px' : '18px' ?>;"></td>	
			<td style="height: <?= $is_download != true ?  '40px' : '18px' ?>;"></td>	
			<td style="height: <?= $is_download != true ?  '40px' : '18px' ?>;"></td>	
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td>Package Price:</td>
			<td style=" text-align: right;"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <?= $package_price?></td>
		</tr>
		
		<tr>
			<td></td>
			<td></td>
			<td>10% Service Charge:</td>
			<td style=" text-align: right;"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <?= $service_charge?></td>
		</tr>

		<tr style="background-color: black; color: white; text-align: center;">
			<th colspan="4">ADDITIONAL CHARGE</th>
		</tr>
		<tr>
			<td ></td>	
			<td >Transportation Fee:</td>	
			<td ></td>	
			<td style=" text-align: right;"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <?= number_format($transportation_fee,2,'.',',')?></td>	
		</tr>
		<tr>
			<td ></td>	
			<td >Additional Hour Fee:</td>	
			<td ></td>	
			<td style=" text-align: right;"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <?= number_format($succeeding_hour_charge,2,'.',',')?></td>	
		</tr>
		<tr>
			<td ></td>	
			<td >Night Differential Fee:</td>		
			<td ></td>	
			<td style=" text-align: right;"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <?= number_format($night_diff_charge,2,'.',',')?></td>
		</tr>
		
		<tr>
			<td style="height: <?= $is_download != true ?  '40px' : '18px' ?>;"></td>	
			<td style="height: <?= $is_download != true ?  '40px' : '18px' ?>;"></td>	
			<td style="height: <?= $is_download != true ?  '40px' : '18px' ?>;"></td>	
			<td style="height: <?= $is_download != true ?  '40px' : '18px' ?>;"></td>	
		</tr>
		
		<tr>
			<td ></td>	
			<td ></td>	
			<td style="color: red;font-weight: bold; text-align: center">Package Total:</td>	
			<td style=" text-align: right;"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <?= number_format($grand_total,2,'.',',')?></td>	
		</tr>
		
		<tr>
			<td colspan="4">**Package price is inclusive of VAT</td>	
		</tr>
	</table>

	<div style="height: 15px; background: black; margin: 0 5px 0 5px">

	</div>


</section>


<?php if($show_terms_and_condition == true): ?>
<div class="page_break"></div>
<style>
#terms-and-condition h1{
	font-weight: bold;
	font-size: 14px;
	padding: 10px 0 10px 0;
	color: white;
}
#terms-and-condition h4, #terms-and-condition p{
	color: black;
	padding: 0;
	margin: 0;
}
#terms-and-condition h4{
	margin-bottom: 4px;
}

#terms-and-condition p{
	font-size: 13px;
}
#terms-and-condition h4{
	text-decoration: underline;
	font-size: 14px;
	font-weight: bold;
}


</style>
<section id="terms-and-condition">
		<div style="background-color: black; color: white; text-align: center;">
			<h1>TERMS AND CONDITIONS</h1>
		</div>
		<div>
			<h4>Billing Arrangement</h4>

			<p>
				Event reservation shall be made at least 15 days before the event date. Client shall be required to sign this contract and make a <u> deposit or 
				down payment of 50% of the total contract price upon signing; 50% balance shall be paid on the day of the event</u>. Payment shall be made in 
				cash or check. Please make check payable to <u><?= $company_name ?></u>
			</p>

			<br>
			
			<h4>Contract Modification</h4>
			<p>
				The guaranteed minimum number indicated in the contract is considered final upon signing of the contract. No refund shall be made if the 
				actual number of guests fall below that of the minimum number of contracted guests. The client shall advise the Caterer of any changes or 
				modifications (number of guests, set up, venue, and the like) at least three (3) days before the actual date. Charges for additional guests 
				shall be made per contract rate. Should the Caterer fail to deliver the agreed number of food for the minimum guaranteed guests for the 
				reason of non-availability of raw materials, the Caterer shall be allowed to replace the menu from among the options presented in the Menu 
				Packages given to the Client subject to prior notice and consent of the client. 
			</p>

			<br>

			<h4>Cancellation Policy</h4>
			
			<p>
				Client is required to submit a formal notice of cancellation to Caterer stating reason of cancellation. Should contract be cancelled;<br>
				• One hundred (100%) of the total deposit shall be refunded if the cancellation is made at least fifteen (15) days prior to event schedule.<br>
				• Fifty percent (50%) of the total deposit shall be refunded if the cancellation is made six (6) days prior to the said event date.<br>
				• No refund shall be made from the total deposit if cancellation is made three (3) days before the event date to cover for the expenses 
				incurred in the preparations for the event.
				<br>
				<br>
				In the event that a refund of down payment is allowed, the refund will be in the form of a check and may be claimed within 10 working days 
				upon approval of refund. 
				
			</p>
			
			<br>

			<h4>Non-Compliance to Payment Scheme</h4>

			<p>
				If the required deposit or down payment is not received on the scheduled date, reservation will be subject to cancellation and notice will be 
				given to the client.Our Company reserves the right to cancel the reservation if the signed contract and down payment are not received seven 
				(7) days before the event date.Should the client fail to pay the balance on the agreed date, a 2% interest per month on the balance shall be 
				applied.
			</p>
			
			<br>
			
			<h4>
				Product Quality
			</h4>
			
			<p>
				The Caterer hereby warrants that the food and beverages that will be served shall all be clean, safe and in good quality. The Caterer shall 
				deliver the agreed quantity of food packages stipulated in this contract and will maintain its high standard of quality for all the products. 
				However, if the food is not consumed within three (3) hours after food is delivered to the guests or within the agreed function time, Client 
				shall assume full responsibility for any damage/s, if any, suffered by the guest concerned attributed to the food or beverage served. Likewise, 
				the Caterer is not liable for any damage/s resulting from other food and/or beverage served outside that which is specified in this Contract. 
			</p>
			
			<br>

			<h4>
				Ingress/Egress
			</h4>
			
			<p>
				All permits and coordination with venue administrator for the ingress and egress shall be the responsibility of the Client. Should there be a 
				need for cooking equipment that requires specific electrical provision, Client will be informed one week prior to the event date. In cases 
				where the venue administrator will charge for the use of the venue facilities, the cost shall be for the account of the Client.
			</p> 
			
			<br>

			<h4>
				Exclusion of Liability
			</h4>

			<p>
				The Caterer will not be held liable for failure to execute obligations specified herein directly or indirectly occasioned by or in consequence of 
				war, change of statutes to the Philippine Government, political issues/disturbances that is/are life and safety threatening, strikes, riots, and 
				other civil disturbances, typhoons, floods, natural calamities and other acts of God, fire or such other conditions and events beyond the 
				control of the company. In the event of non-performance due to any of the above reasons, our company shall refund, in full, payment 
				received for this function.
			</p>
		</div>

		<table  style="width:100%; margin-top: 20px; color: black; font-size: 14px">
			<tr>
				<th style="font-weight: bold; text-align: center;">Prepared by: </th>
				<th style="font-weight: bold; text-align: center;"></th>
				<th style="font-weight: bold; text-align: center;"></th>
				<th style="font-weight: bold; text-align: center;"></th>
				<th style="font-weight: bold; text-align: center;">Approved by: </th>
				<th style="font-weight: bold; text-align: center;"></th>
				<th style="font-weight: bold; text-align: center;"></th>
			</tr>
			<tr>
				<td style="width: 30%; border-bottom: 1px solid black; height: 50px"></td>
				<td style="width: 3%;"></td>
				<td style="width: 13.5%; border-bottom: 1px solid black; height: 50px"></td>
				<td style="width: 7%;"></td>
				<td style="width: 30%; border-bottom: 1px solid black; height: 50px"></td>
				<td style="width: 3%;"></td>
				<td style="width: 13.5%; border-bottom: 1px solid black; height: 50px"></td>
			</tr>
			
			<tr>
				<td></td>
				<td></td>
				<td  style="font-weight: bold; text-align: center;" >Date:</td>
				<td></td>
				<td></td>
				<td></td>
				<td  style="font-weight: bold; text-align: center;" >Date:</td>
			</tr>
			
			<tr>
				<td style="font-weight: bold; text-align: center; font-size: 14px; font-style: italic;" >Taters Representative</td>
				<td></td>
				<td></td>
				<td></td>
				<td  style="font-weight: bold; text-align: center; font-size: 14px; font-style: italic;" >Store Manager / Managing Partner</td>
				<td></td>
				<td></td>
			</tr>
			
			<tr>
				<th style="font-weight: bold; text-align: center; padding-top: 25px">Conforme:</th>
				<th style="font-weight: bold; text-align: center;"></th>
				<th style="font-weight: bold; text-align: center;"></th>
				<th style="font-weight: bold; text-align: center;"></th>
				<th style="font-weight: bold; text-align: center;"></th>
				<th style="font-weight: bold; text-align: center;"></th>
				<th style="font-weight: bold; text-align: center;"></th>
			</tr>
			
			<tr>
				<td style=" width: 30%; border-bottom: 2px solid black; height: 50px"></td>
				<td style=" width: 3%;"></td>
				<td style=" width: 13.5%; border-bottom: 2px solid black; height: 50px"></td>
				<td style=" width: 7%;"></td>
				<td style=" width: 30%; "></td>
				<td style=" width: 3%;"></td>
				<td style="font-weight: bold; width: 13.5%; "></td>
			</tr>
			
			<tr>
				<td></td>
				<td></td>
				<td  style="font-weight: bold; text-align: center;" >Date:</td>
				<td></td>
				<td></td>
				<td></td>
				<td ></td>
			</tr>
			
			<tr>
				<td style="font-weight: bold; text-align: center; font-size: 14px; font-style: italic;" >Client</td>
				<td></td>
				<td></td>
				<td></td>
				<td ></td>
				<td></td>
				<td></td>
			</tr>
		</table>
</section>

<?php endif; ?>
