<?php

echo "<h3>LDAP query test</h3>";
echo "Connecting ...";
$ds=ldap_connect("ldap://scendc001.sita.co.za:389/");  // must be a valid LDAP server!
echo "connect result is " . $ds . "<br />";

if ($ds) { 
    echo "Binding ..."; 
    $r=ldap_bind($ds);     // this is an "anonymous" bind, typically
                           // read-only access
    echo "Bind result is " . $r . "<br />";

    echo "Searching for (sn=S*) ...";
    // Search surname entry
    $sr=ldap_search($ds, "CN=Alfresco,OU=Users,OU=Erasmuskloof,DC=sita,DC=co,DC=za,sn=S*");  
    echo "Search result is " . $sr . "<br />";

    echo "Number of entries returned is " . ldap_count_entries($ds, $sr) . "<br />";

    echo "Getting entries ...<p>";
    $info = ldap_get_entries($ds, $sr);
    echo "Data for " . $info["count"] . " items returned:<p>";

    for ($i=0; $i<$info["count"]; $i++) {
        echo "dn is: " . $info[$i]["dn"] . "<br />";
        echo "first cn entry is: " . $info[$i]["cn"][0] . "<br />";
        echo "first email entry is: " . $info[$i]["mail"][0] . "<br /><hr />";
    }

    echo "Closing connection";
    ldap_close($ds);

} else {
    echo "<h4>Unable to connect to LDAP server</h4>";
}
