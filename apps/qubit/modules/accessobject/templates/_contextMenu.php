<section id="access-objects">
	<h3><?php echo __('Access') ?></h3>
  	<div class="content">
      <?php foreach ($accessObjects as $item): ?>
      
      <table>
		<tr>
			<td>
			  <?php if (isset($item->refusal)): ?>
			  	<?php if ($item->refusal != "Please Select"): ?>
					Refusal: <?php echo $item->refusal ?>
		    	<?php else: ?>
		    		<?php echo "Refusal: -"  ?>	
		  		<?php endif; ?>
	  		  <?php else: ?>
		  		<?php echo "Refusal: -"  ?>	
			  <?php endif; ?>
			</td>
		</tr>
		<tr>
			<td>
			  <?php if (isset($item->sensitivity)): ?>
			  	<?php if ($item->sensitivity != "Please Select"): ?>
					Sensitive: <?php echo $item->sensitivity ?>
		    	<?php else: ?>
		    		<?php echo "Sensitive: -"  ?>	
		  		<?php endif; ?>
	  		  <?php else: ?>
		  		<?php echo "Sensitive: -"  ?>	
			  <?php endif; ?>
			</td>
		</tr>
		<tr>
			<td>
			  <?php if (isset($item->publish)): ?>
			  	<?php if ($item->publish != "Please Select"): ?>
			    	Publish: <?php echo $item->publish ?>
		    	<?php else: ?>
		    		<?php echo "Publish: -"  ?>	
		  		<?php endif; ?>
	  		  <?php else: ?>
		  		<?php echo "Publish: -"  ?>	
			  <?php endif; ?>
			</td>
		</tr>
		<tr>
			<td>
			  <?php if (isset($item->classification)): ?>
			  	<?php if ($item->classification != "Please Select"): ?>
				    Classification: <?php echo $item->classification ?>
		    	<?php else: ?>
		    		<?php echo "Classification: -"  ?>	
		  		<?php endif; ?>
	  		  <?php else: ?>
		  		<?php echo "Classification: -"  ?>	
			  <?php endif; ?>
			</td>
		</tr>
		<tr>
			<td>
			  <?php if (isset($item->restriction)): ?>
			  	<?php if ($item->restriction != "Please Select"): ?>
					Restriction: <?php echo $item->restriction ?>
		    	<?php else: ?>
		    		<?php echo "Restriction: -"  ?>	
		  		<?php endif; ?>
	  		  <?php else: ?>
		  		<?php echo "Restriction: -"  ?>	
			  <?php endif; ?>
			</td>
		</tr>
		<tr>
		  <?php if (isset($item->published)): ?>
			  <?php if ($item->published == 0): ?>
				<?php if ($item->classification == "Public" || $item->sensitivity == "No"): ?>
					<?php echo "<td bgcolor='#FFFFFF'>Published: <font size='3' color='red'><b>Not Published</b></font></td>" ?>
				<?php else: ?>
					<?php echo "<td>Published: <b>Not Publishable</b></td>" ?>
				<?php endif; ?>
			  <?php else: ?>
				<?php echo "<td>Published: <b>Published</b></td>" ?>
			  <?php endif; ?>
		  <?php else: ?>
		  	<?php echo "<td>Published: -</td>" ?>	
		  <?php endif; ?>
		</tr>
      <?php endforeach; ?>
      </table>	
  </div>
</section>
