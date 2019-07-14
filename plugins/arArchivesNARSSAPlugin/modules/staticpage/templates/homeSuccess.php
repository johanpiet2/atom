<div id="homepage-hero" class="row">

  <?php $cacheKey = 'homepage-nav-'.$sf_user->getCulture() ?>
  <?php if (!cache($cacheKey)): ?>
    <div class="span8" id="homepage-nav">
      <p><?php echo __('Browse by') ?></p>
      <ul>
        <?php $icons = array(
          'browseInformationObjects' => '/images/icons-large/icon-archival.png',
          'browseActors' => '/images/icons-large/icon-people.png',
          'browseRepositories' => '/images/icons-large/icon-institutions.png',
          'browseAccession' => '/images/icons-large/icon-accessions.png',
          'browseSubjects' => '/images/icons-large/icon-subjects.png',
          'browseFunctions' => '/images/icons-large/icon-functions.png',
          'browsePlaces' => '/images/icons-large/icon-places.png',
          'browseDigitalObjects' => '/images/icons-large/icon-media.png') ?>
        <?php $browseMenu = QubitMenu::getById(QubitMenu::BROWSE_ID) ?>
        <?php if ($browseMenu->hasChildren()): ?>
          <?php foreach ($browseMenu->getChildren() as $item): ?>
            <li>
              <a href="<?php echo url_for($item->getPath(array('getUrl' => true, 'resolveAlias' => true))) ?>">
                <?php if (isset($icons[$item->name])): ?>
                  <?php echo image_tag($icons[$item->name], array('width' => 42, 'height' => 42)) ?>
                <?php endif; ?>
                <?php echo $item->getLabel(array('cultureFallback' => true)) ?>
              </a>
            </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </div>
    <?php cache_save($cacheKey) ?>
  <?php endif; ?>

  <div class="span3" id="intro">
    <?php if ('fr' == $sf_user->getCulture()): ?>
      <h2>
        <span class="title">Archives</span>
        Votre accèss à l’histoire du ?
      </h2>
      <p>ARCHIVES.org.za</p>
    <?php else: ?>
      <h2>
        <span class="title">AtoM/National Database</span>
        The Gateway to South Africa's Past
      </h2>
      <p>AtoM feeding the National Database is your gateway to resources in archives across South Africa:<br />Through this gateway, search for details (descriptions) about archival materials, find digital images, visit virtual exhibits, browse information about archives in every province and territory, and discover the archives with the information you need. The National Database is your national portal to RSA documentary heritage, found in various archives.</p>
    <?php endif; ?>
  </div>

</div>
