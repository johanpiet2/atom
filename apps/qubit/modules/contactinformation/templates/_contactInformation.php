<section class="vcard">

  <?php if (!empty($contactInformation->contactPerson)): ?>
    <div class="field">
      <h3>&nbsp;</h3>
      <div class="agent">
        <?php echo render_value($contactInformation->contactPerson) ?>
		<?php if (!empty($contactInformation->position)): ?>
        	<?php echo " - " . render_value($contactInformation->position) ?>
        <?php endif; ?>
        <?php if ($contactInformation->primaryContact): ?>
          <span class="primary-contact">
            <?php echo __('Primary contact') ?>
          </span>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>

  <div class="field">
    <h3><?php echo __('Type') ?></h3>
    <div class="type">
      <?php echo render_value_inline($contactInformation->getContactType(array('cultureFallback' => true))) ?>
    </div>
  </div>

  <div class="field adr">

    <h3><?php echo __('Address') ?></h3>

    <div>

      <div class="field">
        <h3><?php echo __('Street address') ?></h3>
        <div class="street-address">
          <?php echo render_value_inline($contactInformation->streetAddress) ?>
        </div>
      </div>

      <div class="field">
        <h3><?php echo __('Locality') ?></h3>
        <div class="locality">
          <?php echo render_value_inline($contactInformation->getCity(array('cultureFallback' => true))) ?>
        </div>
      </div>

      <div class="field">
        <h3><?php echo __('Region') ?></h3>
        <div class="region">
          <?php echo render_value_inline($contactInformation->getRegion(array('cultureFallback' => true))) ?>
        </div>
      </div>

      <div class="field">
        <h3><?php echo __('Country name') ?></h3>
        <div class="country-name">
          <?php echo format_country($contactInformation->countryCode) ?>
        </div>
      </div>

      <div class="field">
        <h3><?php echo __('Postal code') ?></h3>
        <div class="postal-code">
          <?php echo render_value_inline($contactInformation->postalCode) ?>
        </div>
      </div>

	  <!-- jjp SITA 17 Dec 2014-->
      <div class="field">
        <h3><?php echo __('Postal Address') ?></h3>
        <div class="street-address">
          <?php echo render_value($contactInformation->postalAddress) ?>
        </div>
      </div>

      <div class="field">
        <h3><?php echo __('Postal Locality') ?></h3>
        <div class="locality">
          <?php echo render_value($contactInformation->getPostalCity(array('cultureFallback' => true))) ?>
        </div>
      </div>

      <div class="field">
        <h3><?php echo __('Postal Region') ?></h3>
        <div class="region">
          <?php echo render_value($contactInformation->getPostalRegion(array('cultureFallback' => true))) ?>
        </div>
      </div>

      <div class="field">
        <h3><?php echo __('Postal Country name') ?></h3>
        <div class="country-name">
          <?php echo format_country($contactInformation->postalCountryCode) ?>
        </div>
      </div>

      <div class="field">
        <h3><?php echo __('Postal Post code') ?></h3>
        <div class="postal-code">
          <?php echo render_value($contactInformation->postalPostCode) ?>
        </div>
      </div>

    </div>

  </div>

  <div class="field">
    <h3><?php echo __('Telephone') ?></h3>
    <div class="tel">
      <?php echo render_value_inline($contactInformation->telephone) ?>
    </div>
  </div>

  <div class="field">
    <h3 class="type"><?php echo __('Fax') ?></h3>
    <div class="fax">
      <?php echo render_value_inline($contactInformation->fax) ?>
    </div>
  </div>

  <!-- jjp SITA 17 Dec 2014-->
  <div class="field">
    <h3 class="type"><?php echo __('Cell') ?></h3>
    <div class="cell">
      <?php echo render_value_inline($contactInformation->cell) ?>
    </div>
  </div>

  <div class="field">
    <h3><?php echo __('Email') ?></h3>
    <div class="email">
      <?php echo render_value_inline($contactInformation->email) ?>
    </div>
  </div>

  <div class="field">
    <h3><?php echo __('URL') ?></h3>
    <div class="url">
      <?php echo render_value_inline($contactInformation->website) ?>
    </div>
  </div>

  <div class="field">
    <h3><?php echo __('Note') ?></h3>
    <div class="note">
      <?php echo render_value_inline($contactInformation->getNote(array('cultureFallback' => true))) ?>
    </div>
  </div>

  <div class="field">
    <h3><?php echo __('Latitude') ?></h3>
    <div class="url">
      <?php echo render_value_inline($contactInformation->latitude) ?>
    </div>
  </div>

  <div class="field">
    <h3><?php echo __('Longitude') ?></h3>
    <div class="url">
      <?php echo render_value_inline($contactInformation->longitude) ?>
    </div>
  </div>

</section>
