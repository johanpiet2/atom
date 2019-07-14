<?php echo get_component_slot('header') ?>

<?php echo get_component('default', 'updateCheck') ?>

<?php if ($sf_user->isAuthenticated()): ?>
  <div id="top-bar">
    <nav>
      <?php echo get_component('menu', 'userMenu') ?>
      <?php echo get_component('menu', 'quickLinksMenu') ?>
      <?php echo get_component('menu', 'changeLanguageMenu') ?>
      <?php echo get_component('menu', 'mainMenu', array('sf_cache_key' => $sf_user->getCulture().$sf_user->getUserID())) ?>
    </nav>
  </div>
<?php endif; ?>

<div id="header">
	<div class="container" style="padding: 98px 0px 00px 0px;">
		<div class="span6">
			<div id="header-search" class="span7" style="align:left">
				<?php echo get_component('search', 'box') ?>
			</div>
		</div>
		<div class="span4" style="align:right">
			<ul id="header-nav" class="nav nav-pills">
				<li style="padding: 11px 0px 00px 0px; color: @red; background-color: @grayDark;"><?php echo link_to(__('Home'), '@homepage') ?></li>
				<?php if ('fr' == $sf_user->getCulture()): ?>
					<li><?php echo link_to(__('Contactez-nous'), array('module' => 'staticpage', 'slug' => 'contact')) ?></li>
				<?php else: ?>
					<li><?php //echo link_to(__('Contact us'), array('module' => 'staticpage', 'slug' => 'contact')) ?></li>
				<?php endif; ?>

				<!--?php foreach (array('en', 'fr') as $item): ?-->
				<?php foreach (array('en') as $item): ?>
					<?php if ($sf_user->getCulture() != $item): ?>
						<li><?php echo link_to(format_language($item, $item), array('sf_culture' => $item) + $sf_request->getParameterHolder()->getAll()) ?></li>
						<?php break; ?>
					<?php endif; ?>
				<?php endforeach; ?>

				<?php if (!$sf_user->isAuthenticated()): ?>
					<li style="padding: 11px 0px 00px 0px; color: @red; background-color: @grayDark;"><?php echo link_to(__('Log in'), array('module' => 'user', 'action' => 'login')) ?></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div>
