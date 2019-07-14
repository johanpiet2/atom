<section class="actions">
  <ul>
    <?php if ($resource->id != QubitInformationObject::ROOT_ID): ?>
      <li><?php echo link_to(__('Cancel'), array($resource, 'module' => 'informationobject'), array('class' => 'c-btn')) ?></li>
      <?php if (isset($sf_request->parent)): ?>
        <li><input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Create') ?>"/></li>
      <?php else: ?>
        <li><input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Save') ?>"/></li>
      <?php endif; ?>
    <?php else: ?>
      <li><?php echo link_to(__('Cancel'), array('module' => 'informationobject', 'action' => 'browse'), array('class' => 'c-btn')) ?></li>
      <li><input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Create') ?>"/></li>
    <?php endif; ?>
  </ul>
 <?php
	$pageContentInc = "../phpspellcheck_root/include.php"; 
	if(file_exists($pageContentInc)){
	   // echo;
		require "../phpspellcheck_root/include.php";	//   "phpspellcheck/include.php" // Full file path to the include.php file in the phpspellcheck Folder

		$mySpell = new SpellCheckButton();
		$mySpell->InstallationPath = "/phpspellcheck_root/";	  // "/phpspellcheck/" //  Relative URL of phpspellcheck within your site
		$mySpell->Fields =  "identifier,title,levelOfDescription,extentAndMedium,archivalHistory,acquisition,scopeAndContent,appraisal,accruals,arrangement,accessConditions,reproductionConditions,languageNotes,physicalCharacteristics,findingAids,locationOfOriginals,locationOfCopies,relatedUnitsOfDescription,descriptionIdentifier,institutionResponsibleIdentifier,revisionHistory,isadNotes_0_content,rules,isadPublicationNotes_0_content,sources,isadArchivistsNotes_0_content,partNumber";	  // id or 'ALL' or 'EDITORS' or "TEXTAREA" or 'TEXTINPUTS'
		echo $mySpell->SpellImageButton();	  // Render
		
		$mySpell = new SpellAsYouType();
		$mySpell->InstallationPath = "/phpspellcheck_root/"; 	  
		$mySpell->Fields = "identifier,title,levelOfDescription,extentAndMedium,archivalHistory,acquisition,scopeAndContent,appraisal,accruals,arrangement,accessConditions,reproductionConditions,languageNotes,physicalCharacteristics,findingAids,locationOfOriginals,locationOfCopies,relatedUnitsOfDescription,descriptionIdentifier,institutionResponsibleIdentifier,revisionHistory,isadNotes_0_content,rules,isadPublicationNotes_0_content,sources,isadArchivistsNotes_0_content,partNumber";
		echo $mySpell->Activate();
	}else
	{
		echo "Include Not Found Error!!!!!!";
	}  
?>
</section>
