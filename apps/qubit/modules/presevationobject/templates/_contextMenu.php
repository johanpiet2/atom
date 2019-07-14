<section id="presevation-objects">
<h3><?php echo __('Preservation') ?></h3>
  <div class="content">
      <?php foreach ($presevationObjects as $item): ?>
      <table>
		<tr>
			<td>
			  <?php if (isset($item->usability)): ?>
			  	<?php if ($item->usability != "Please Select"): ?>
					Usability: <?php echo $item->usability ?>
		    	<?php else: ?>
		    		<?php echo "Usability: -"  ?>	
		  		<?php endif; ?>
	  		  <?php else: ?>
		  		<?php echo "Usability: -"  ?>	
			  <?php endif; ?>
			</td>
		</tr>
		<tr>
			<td>
			  <?php if (isset($item->measure)): ?>
			  	<?php if ($item->measure != "Please Select"): ?>
					Measure: <?php echo $item->measure ?>
		    	<?php else: ?>
		    		<?php echo "Measure: -"  ?>	
		  		<?php endif; ?>
	  		  <?php else: ?>
		  		<?php echo "Measure: -"  ?>	
			  <?php endif; ?>
			</td>
		</tr>
		<tr>
			<td>
			  <?php if (isset($item->availability)): ?>
			  	<?php if ($item->availability != "Please Select"): ?>
					Available: <?php echo $item->availability ?>
		    	<?php else: ?>
		    		<?php echo "Available: -"  ?>	
		  		<?php endif; ?>
	  		  <?php else: ?>
		  		<?php echo "Available: -"  ?>	
			  <?php endif; ?>
			</td>
		</tr>
		<tr>
			<td>
			  <?php if (isset($item->restoration)): ?>
			  	<?php if ($item->restoration != "Please Select"): ?>
					Restoration: <?php echo $item->restoration ?>
		    	<?php else: ?>
		    		<?php echo "Restoration: -"  ?>	
		  		<?php endif; ?>
	  		  <?php else: ?>
		  		<?php echo "Restoration: -"  ?>	
			  <?php endif; ?>
			</td>
		</tr>
		<tr>
			<td>
			  <?php if (isset($item->conservation)): ?>
			  	<?php if ($item->conservation != "Please Select"): ?>
					Conservation: <?php echo $item->conservation ?>
		    	<?php else: ?>
		    		<?php echo "Conservation: -"  ?>	
		  		<?php endif; ?>
	  		  <?php else: ?>
		  		<?php echo "Conservation: -"  ?>	
			  <?php endif; ?>
			</td>
		</tr>
		<tr>
			<td>
			  <?php if (isset($item->type)): ?>
			  	<?php if ($item->type != "Please Select"): ?>
					Method of Storage: <?php echo $item->type ?>
		    	<?php else: ?>
		    		<?php echo "Method of Storage: -"  ?>	
		  		<?php endif; ?>
	  		  <?php else: ?>
		  		<?php echo "Method of Storage: -"  ?>	
			  <?php endif; ?>
			</td>
		</tr>
		<tr>
			<td>
			  <?php if (isset($item->condition)): ?>
			  	<?php if ($item->condition != "Please Select"): ?>
					Condition: <?php echo $item->condition ?>
		    	<?php else: ?>
		    		<?php echo "Condition: -"  ?>	
		  		<?php endif; ?>
	  		  <?php else: ?>
		  		<?php echo "Condition: -"  ?>	
			  <?php endif; ?>
			</td>
		</tr>
      <?php endforeach; ?>
      </table>	
  </div>
</section>
