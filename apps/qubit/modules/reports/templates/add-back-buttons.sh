#!/bin/bash

echo "Adding 'Back to Reports' buttons to all report templates..."

cd /usr/share/nginx/atom_psis/apps/qubit/modules/reports/templates

# Function to add back button to sidebar
add_back_button() {
    local file=$1
    
    # Check if back button already exists
    if grep -q "Back to Reports" "$file"; then
        echo "✓ $file already has back button"
        return
    fi
    
    # Add back button after sidebar widget opening
    sed -i '/<section class="sidebar-widget">/a\    \n    <div style="margin-bottom: 1rem;">\n      <a href="<?php echo url_for(['"'"'module'"'"' => '"'"'reports'"'"', '"'"'action'"'"' => '"'"'reportSelect'"'"']); ?>" class="c-btn" style="width:100%;">\n        <i class="fa fa-arrow-left"></i> <?php echo __('\''Back to Reports'\''); ?>\n      </a>\n    </div>\n' "$file"
    
    echo "✓ Added back button to $file"
}

# Add to all report templates
add_back_button "reportDonorSuccess.php"
add_back_button "reportRepositorySuccess.php"
add_back_button "reportInformationObjectSuccess.php"

# Clear cache
cd /usr/share/nginx/atom_psis
php symfony cc

echo ""
echo "✅ All done! Back buttons added to all reports."
