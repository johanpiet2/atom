<?xml version="1.0"?>

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output method="html" indent="yes" />
	<xsl:template match="/repository">
		<html>
			<head></head>
			<body>
				<table border="0px" bordercolor="#669999" cellspacing="1" cellpadding="2">
					<tr>
						<td bordercolor="#7F8B8B" colspan="2"><font color="#474C4C" size="14"><b>
							<xsl:choose>
								<xsl:when test="repositoryinfo/unitTitle">
									<xsl:value-of select="repositoryinfo/unitTitle" />
								</xsl:when>
								<xsl:otherwise>
								</xsl:otherwise>
							</xsl:choose>
						</b></font></td>
					</tr>
					<xsl:apply-templates />
				</table>
			</body>
		</html>
	</xsl:template>
	
	<xsl:template match="repository/repositoryinfo">
<!-- Repository Details ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
		<tr>
			<td bordercolor="#7F8B8B" colspan="2"><font color="#FF9933" size="12"><b>Repository</b></font></td>
		</tr>
		<xsl:choose>
			<xsl:when test="repositoryName">
				<tr>
					<td><font color="#293D3D" size="10">Name:</font></td>
					<td align="left"><xsl:value-of select="repositoryName" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repositoryid">
				<tr>
					<td><font color="#293D3D" size="10">ID:</font></td>
					<td align="left"><xsl:value-of select="repositoryid" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="identifier">
				<tr>
					<td><font color="#293D3D" size="10">Identifier:</font></td>
					<td align="left"><xsl:value-of select="identifier" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="authorizedFormOfNameFamily">
				<tr>
					<td><font color="#293D3D" size="10">Authorized Form of Name (Family):</font></td>
					<td align="left"><xsl:value-of select="authorizedFormOfNameFamily" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="authorizedFormOfNamePerson">
				<tr>
					<td><font color="#293D3D" size="10">Authorized Form of Name (Person):</font></td>
					<td align="left"><xsl:value-of select="authorizedFormOfNamePerson" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="authorizedFormOfNameCorporate">
				<tr>
					<td><font color="#293D3D" size="10">Authorized Form of Name (Corporate):</font></td>
					<td align="left"><xsl:value-of select="authorizedFormOfNameCorporate" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="authorizedFormOfName">
				<tr>
					<td><font color="#293D3D" size="10">Authorized Form of Name (Other):</font></td>
					<td align="left"><xsl:value-of select="authorizedFormOfName" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//address/primarycontact">
				<tr>
					<td><font color="#293D3D" size="10">Primary Contact:</font></td>
					<td align="left"><xsl:value-of select="//address/primarycontact" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//address/title">
				<tr>
					<td><font color="#293D3D" size="10">Contact Title:</font></td>
					<td><xsl:value-of select="//address/title" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//address/contactperson">
				<tr>
					<td><font color="#293D3D" size="10">Contact Person:</font></td>
					<td><xsl:value-of select="//address/contactperson" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//address/position">
				<tr>
					<td><font color="#293D3D" size="10">Position:</font></td>
					<td><xsl:value-of select="//address/position" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//address/contacttype">
				<tr>
					<td><font color="#293D3D" size="10">Contact Type:</font></td>
					<td><xsl:value-of select="//address/contacttype" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//address/streetaddress">
				<tr>
					<td><font color="#293D3D" size="10">Street Address:</font></td>
					 <td><xsl:value-of select="//address/streetaddress" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//address/city">
				<tr>
					<td><font color="#293D3D" size="10">City:</font></td>
					 <td><xsl:value-of select="//address/city" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//address/region">
				<tr>
					<td><font color="#293D3D" size="10">Locality/Region:</font></td>
					 <td><xsl:value-of select="//address/region" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//address/postalcode">
				<tr>
					<td><font color="#293D3D" size="10">Postal Code:</font></td>
					 <td><xsl:value-of select="//address/postalcode" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//address/postaladdress">
				<tr>
					<td><font color="#293D3D" size="10">Postal Address:</font></td>
					 <td><xsl:value-of select="//address/postaladdress" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//address/postalcity">
				<tr>
					<td><font color="#293D3D" size="10">Postal City:</font></td>
					 <td><xsl:value-of select="//address/postalcity" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//address/postalregion">
				<tr>
					<td><font color="#293D3D" size="10">Postal Locality/Region:</font></td>
					 <td><xsl:value-of select="//address/postalregion" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//address/postalpostcode">
				<tr>
					<td><font color="#293D3D" size="10">Post Code:</font></td>
					 <td><xsl:value-of select="//address/postalpostcode" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//address/latitude">
				<tr>
					<td><font color="#293D3D" size="10">Latitude:</font></td>
					 <td><xsl:value-of select="//address/latitude" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//address/longitude">
				<tr>
					<td><font color="#293D3D" size="10">Longitude:</font></td>
					 <td><xsl:value-of select="//address/longitude" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//address/note">
				<tr>
					<td><font color="#293D3D" size="10">Note:</font></td>
					 <td><xsl:value-of select="//address/note" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//address/telephone">
				<tr>
					<td><font color="#293D3D" size="10">Telephone:</font></td>
					 <td><xsl:value-of select="//address/telephone" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//address/fax">
				<tr>
					<td><font color="#293D3D" size="10">Fax:</font></td>
					 <td><xsl:value-of select="//address/fax" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//address/cell">
				<tr>
					<td><font color="#293D3D" size="10">Cell:</font></td>
					 <td><xsl:value-of select="//address/cell" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//address/email">
				<tr>
					<td><font color="#293D3D" size="10">e-Mail:</font></td>
					 <td><xsl:value-of select="//address/email" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//address/website">
				<tr>
					<td><font color="#293D3D" size="10">Website:</font></td>
					 <td><xsl:value-of select="//address/website" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repoHistory">
				<tr>
					<td><font color="#293D3D" size="10">History:</font></td>
					 <td><xsl:value-of select="repoHistory" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>



		<xsl:choose>
			<xsl:when test="repoGeoculturalContext">
				<tr>
					<td><font color="#293D3D" size="10">Geocultural Context:</font></td>
					 <td><xsl:value-of select="repoGeoculturalContext" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repoMandates">
				<tr>
					<td><font color="#293D3D" size="10">Mandates:</font></td>
					 <td><xsl:value-of select="repoMandates" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repoInternalStructures">
				<tr>
					<td><font color="#293D3D" size="10">Internal Structures:</font></td>
					 <td><xsl:value-of select="repoInternalStructures" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repoCollectingPolicies">
				<tr>
					<td><font color="#293D3D" size="10">Collecting Policies:</font></td>
					 <td><xsl:value-of select="repoCollectingPolicies" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repoBuildings">
				<tr>
					<td><font color="#293D3D" size="10">Buildings:</font></td>
					 <td><xsl:value-of select="repoBuildings" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repoHoldings">
				<tr>
					<td><font color="#293D3D" size="10">Holdings:</font></td>
					 <td><xsl:value-of select="repoHoldings" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repoFindingAids">
				<tr>
					<td><font color="#293D3D" size="10">Finding Aids:</font></td>
					 <td><xsl:value-of select="repoFindingAids" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repoOpeningTimes">
				<tr>
					<td><font color="#293D3D" size="10">Opening Times:</font></td>
					 <td><xsl:value-of select="repoOpeningTimes" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repoAccessConditions">
				<tr>
					<td><font color="#293D3D" size="10">Access Conditions:</font></td>
					 <td><xsl:value-of select="repoAccessConditions" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repoDisabledAccess">
				<tr>
					<td><font color="#293D3D" size="10">Disabled Access:</font></td>
					 <td><xsl:value-of select="repoDisabledAccess" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>




		<xsl:choose>
			<xsl:when test="repoResearchServices">
				<tr>
					<td><font color="#293D3D" size="10">Research Services:</font></td>
					 <td><xsl:value-of select="repoResearchServices" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repoReproductionServices">
				<tr>
					<td><font color="#293D3D" size="10">Reproduction Services:</font></td>
					 <td><xsl:value-of select="repoReproductionServices" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repoPublicFacilities">
				<tr>
					<td><font color="#293D3D" size="10">Public Facilities:</font></td>
					 <td><xsl:value-of select="repoPublicFacilities" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repoDescIdentifier">
				<tr>
					<td><font color="#293D3D" size="10">Description Identifier:</font></td>
					 <td><xsl:value-of select="repoDescIdentifier" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repoDescInstitutionIdentifier">
				<tr>
					<td><font color="#293D3D" size="10">Description Institution Identifier:</font></td>
					 <td><xsl:value-of select="repoDescInstitutionIdentifier" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repoDescRules">
				<tr>
					<td><font color="#293D3D" size="10">Description Rules:</font></td>
					 <td><xsl:value-of select="repoDescRules" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repoDescStatus">
				<tr>
					<td><font color="#293D3D" size="10">Description Status:</font></td>
					 <td><xsl:value-of select="repoDescStatus" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repoDescDetail">
				<tr>
					<td><font color="#293D3D" size="10">Description Detail:</font></td>
					 <td><xsl:value-of select="repoDescDetail" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repoDescRevisionHistory">
				<tr>
					<td><font color="#293D3D" size="10">Description Revision History:</font></td>
					 <td><xsl:value-of select="repoDescRevisionHistory" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>


		<xsl:choose>
			<xsl:when test="repoLanguage">
				<tr>
					<td><font color="#293D3D" size="10">Repository Language:</font></td>
					 <td><xsl:value-of select="repoLanguage" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repoScript">
				<tr>
					<td><font color="#293D3D" size="10">Script:</font></td>
					 <td><xsl:value-of select="repoScript" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repoDescSources">
				<tr>
					<td><font color="#293D3D" size="10">Description Sources:</font></td>
					 <td><xsl:value-of select="repoDescSources" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repoMaintenanceNotes">
				<tr>
					<td><font color="#293D3D" size="10">Maintenance Notes:</font></td>
					 <td><xsl:value-of select="repoMaintenanceNotes" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>

		<!-- First Item / Identifier ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
		<p style="page-break-before: always"/>
		<tr>
			<td bordercolor="#7F8B8B" colspan="2"><font color="#FF9933" size="12"><b>Identity</b></font></td>
		</tr>
		<xsl:choose>
			<xsl:when test="publicationdate">
				<tr>
					<td>Publication Date:</td>
					<td align="left"><xsl:value-of select="publicationdate" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="unitTitle">
				<tr>
					<td><h3>Title:</h3></td>
					<td align="left"><h3><xsl:value-of select="unitTitle" /></h3></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
				<!-- No node exists -->
				
			</xsl:otherwise>
		</xsl:choose>

		<xsl:choose>
			<xsl:when test="referenceCode">
				<tr>
					<td>Reference Code:</td>
					<td align="left"><xsl:value-of select="referenceCode" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="startDate">
				<tr>
					<td>Start Date:</td>
					<td align="left"><xsl:value-of select="startDate" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="endDate">
				<tr>
					<td>End Date:</td>
					<td align="left"><xsl:value-of select="endDate" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="dateRange">
				<tr>
					<td>Date Range:</td>
					<td align="left"><xsl:value-of select="dateRange" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="extendAndMedium">
				<tr>
					<td>Extend and Medium:</td>
					<td align="left"><xsl:value-of select="extendAndMedium" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="repository">
				<tr>
					<td>Repository:</td>
					<td align="left"><xsl:value-of select="repository" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="language">
				<tr>
					<td>Language:</td>
					<td align="left"><xsl:value-of select="language" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="scriptCode">
				<tr>
					<td>Script Code:</td>
					<td align="left"><xsl:value-of select="scriptCode" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="languageAndScriptNotes">
				<tr>
					<td>Language and Script Notes:</td>
					<td align="left"><xsl:value-of select="languageAndScriptNotes" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="sources">
				<tr>
					<td>Sources:</td>
					<td align="left"><xsl:value-of select="sources" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="noteTypeId">
				<tr>
					<td>Note Type ID:</td>
					<td align="left"><xsl:value-of select="noteTypeId" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="note">
				<tr>
					<td>Archivist's Note:</td>
					<td align="left"><xsl:value-of select="note" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="history">
				<tr>
					<td>History:</td>
					<td align="left"><xsl:value-of select="history" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="authorizedFormOfNameFamily">
				<tr>
					<td><font color="#293D3D" size="10">Authorized Form of Name (Family):</font></td>
					<td align="left"><xsl:value-of select="authorizedFormOfNameFamily" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="authorizedFormOfNamePerson">
				<tr>
					<td><font color="#293D3D" size="10">Authorized Form of Name (Person):</font></td>
					<td align="left"><xsl:value-of select="authorizedFormOfNamePerson" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="authorizedFormOfNameCorporate">
				<tr>
					<td><font color="#293D3D" size="10">Repository Authorized Form of Name (Corporate):</font></td>
					<td align="left"><xsl:value-of select="authorizedFormOfNameCorporate" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="authorizedFormOfName">
				<tr>
					<td><font color="#293D3D" size="10">Repository Authorized Form of Name (Other):</font></td>
					<td align="left"><xsl:value-of select="authorizedFormOfName" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="datesOfExistence">
				<tr>
					<td>Dates of Existence:</td>
					<td align="left"><xsl:value-of select="datesOfExistence" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="publicationStatus">
				<tr>
					<td>Publication Status:</td>
					<td align="left"><xsl:value-of select="publicationStatus" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="levelOfDescription">
				<tr>
					<td>Level of Description:</td>
					<td align="left"><xsl:value-of select="levelOfDescription" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="levelOfDetail">
				<tr>
					<td>Level of Detail:</td>
					<td align="left"><xsl:value-of select="levelOfDetail" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="descriptionStatus">
				<tr>
					<td>Description Status:</td>
					<td align="left"><xsl:value-of select="descriptionStatus" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="descriptionIdentifier">
				<tr>
					<td>Description Identifier:</td>
					<td align="left"><xsl:value-of select="descriptionIdentifier" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="institutionResponsibleIdentifier">
				<tr>
					<td>Institution Responsible Identifier:</td>
					<td align="left"><xsl:value-of select="institutionResponsibleIdentifier" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="scopeAndContent">
				<tr>
					<td>Scope and Content:</td>
					<td align="left"><xsl:value-of select="scopeAndContent" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="arrangement">
				<tr>
					<td>Arrangement:</td>
					<td align="left"><xsl:value-of select="arrangement" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="entityTypeIdCorporate">
				<tr>
					<td>Role and Entity Type ID (Corporate):</td>
					<td align="left"><xsl:value-of select="entityTypeIdCorporate" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="physicalCharacteristics">
				<tr>
					<td>Physical Characteristics:</td>
					<td align="left"><xsl:value-of select="physicalCharacteristics" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="appraisal">
				<tr>
					<td>Appraisal:</td>
					<td align="left"><xsl:value-of select="appraisal" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="acquisition">
				<tr>
					<td>Acquisition:</td>
					<td align="left"><xsl:value-of select="acquisition" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="accruals">
				<tr>
					<td>Accruals:</td>
					<td align="left"><xsl:value-of select="accruals" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="archivalHistory">
				<tr>
					<td>Archival History:</td>
					<td align="left"><xsl:value-of select="archivalHistory" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="revisionHistory">
				<tr>
					<td>Revision History:</td>
					<td align="left"><xsl:value-of select="revisionHistory" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="locationOfOriginals">
				<tr>
					<td>Location of Originals:</td>
					<td align="left"><xsl:value-of select="locationOfOriginals" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="locationOfCopies">
				<tr>
					<td>Location of Copies:</td>
					<td align="left"><xsl:value-of select="locationOfCopies" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="relatedUnitsOfDescription">
				<tr>
					<td>Related Units of Description:</td>
					<td align="left"><xsl:value-of select="relatedUnitsOfDescription" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="accessConditions">
				<tr>
					<td>Access Conditions:</td>
					<td align="left"><xsl:value-of select="accessConditions" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="reproductionConditions">
				<tr>
					<td>Reproduction Conditions:</td>
					<td align="left"><xsl:value-of select="reproductionConditions" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="findingAids">
				<tr>
					<td>Finding Aids:</td>
					<td align="left"><xsl:value-of select="findingAids" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="bibliographyPublicationNotes">
				<tr>
					<td>Bibliography Publication Notes:</td>
					<td align="left"><xsl:value-of select="bibliographyPublicationNotes" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		
		<xsl:for-each select="creators">
		    <p style="page-break-before: always"/>
			<xsl:choose>
				<xsl:when test="descendantUnitTitle">
					<tr>
						<td align="left" colspan="2"><h2>Descendant</h2></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantReferenceCode">
					<tr>
						<td>Reference Code:</td>
						<td align="left"><xsl:value-of select="descendantReferenceCode" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantUnitTitle">
					<tr>
						<td><h3>Title:</h3></td>
						<td align="left"><h3><xsl:value-of select="descendantUnitTitle" /></h3></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
		<xsl:choose>
			<xsl:when test="descendantLevelOfDescription">
				<tr>
					<td>Level of Description:</td>
					<td align="left"><xsl:value-of select="descendantLevelOfDescription" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
				<tr>
					<td>Level of Description:</td>
					<td align="left">..</td>
				</tr>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="descendantLevelOfDetail">
				<tr>
					<td>Level of Detail:</td>
					<td align="left"><xsl:value-of select="descendantLevelOfDetail" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantRepositoryCountryCode">
					<tr>
						<td>Repository Country Code:</td>
						<td align="left"><xsl:value-of select="descendantRepositoryCountryCode" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantRepositoryid">
					<tr>
						<td>Repository ID:</td>
						<td align="left"><xsl:value-of select="descendantRepositoryid" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantStartDate">
					<tr>
						<td>Start Date:</td>
						<td align="left"><xsl:value-of select="descendantStartDate" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantEndDate">
					<tr>
						<td>End Date:</td>
						<td align="left"><xsl:value-of select="descendantEndDate" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantDateRange">
					<tr>
						<td>Date Range:</td>
						<td align="left"><xsl:value-of select="descendantDateRange" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantExtentAndMedium">
					<tr>
						<td>Extent and Medium:</td>
						<td align="left"><xsl:value-of select="descendantExtentAndMedium" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="//1descendantAddress/primarycontact"> <!-- change //1 to // comment out... -->
					<tr>
						<td>Primary Contact:</td>
						<td align="left"><xsl:value-of select="//descendantAddress/primarycontact" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="//1descendantAddress/title">
					<tr>
						<td>Title:</td>
						<td><xsl:value-of select="//descendantAddress/title" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="//1descendantAddress/contactperson">
					<tr>
						<td>Contact Person:</td>
						<td><xsl:value-of select="//descendantAddress/contactperson" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="//1descendantAddress/position">
					<tr>
						<td>Position:</td>
						<td><xsl:value-of select="//descendantAddress/position" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="//1descendantAddress/contacttype">
					<tr>
						<td>Contact Type:</td>
						<td><xsl:value-of select="//descendantAddress/contacttype" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="//1descendantAddress/streetaddress">
					<tr>
						<td>Street Address:</td>
						<td><xsl:value-of select="//descendantAddress/streetaddress" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="//1descendantAddress/city">
					<tr>
						<td>City:</td>
						<td><xsl:value-of select="//descendantAddress/city" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="//1descendantAddress/region">
					<tr>
						<td>Locale/Region:</td>
						<td><xsl:value-of select="//descendantAddress/region" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="//1descendantAddress/postalcode">
					<tr>
						<td>Postal Code:</td>
						<td><xsl:value-of select="//descendantAddress/postalcode" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="//1descendantAddress/postaladdress">
					<tr>
						<td>Postal Address:</td>
						<td><xsl:value-of select="//descendantAddress/postaladdress" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="//1descendantAddress/postalcity">
					<tr>
						<td>City:</td>
						<td><xsl:value-of select="//descendantAddress/postalcity" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="//1descendantAddress/postalregion">
					<tr>
						<td>Region:</td>
						<td><xsl:value-of select="//descendantAddress/postalregion" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="//1descendantAddress/postalpostcode">
					<tr>
						<td>Post Code:</td>
						<td><xsl:value-of select="//descendantAddress/postalpostcode" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="//1descendantAddress/longitude">
					<tr>
						<td>Longitude:</td>
						<td><xsl:value-of select="//descendantAddress/longitude" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="//1descendantAddress/latitude">
					<tr>
						<td>Latitude:</td>
						<td><xsl:value-of select="//descendantAddress/latitude" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="//1descendantAddress/note">
					<tr>
						<td>Note:</td>
						<td><xsl:value-of select="//descendantAddress/note" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="//1descendantAddress/telephone">
					<tr>
						<td>Telephone:</td>
						<td><xsl:value-of select="//descendantAddress/telephone" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="//1descendantAddress/fax">
					<tr>
						<td>Fax:</td>
						<td><xsl:value-of select="//descendantAddress/fax" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="//1descendantAddress/cell">
					<tr>
						<td>Cell:</td>
						<td><xsl:value-of select="//descendantAddress/cell" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="//1descendantAddress/email">
					<tr>
						<td>e-Mail:</td>
						<td><xsl:value-of select="//descendantAddress/email" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="//1descendantAddress/website">
					<tr>
						<td>Website:</td>
						<td><xsl:value-of select="//descendantAddress/website" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantLanguage">
					<tr>
						<td>Descendant Language:</td>
						<td align="left"><xsl:value-of select="descendantLanguage" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantScriptCode">
					<tr>
						<td>Script Code:</td>
						<td align="left"><xsl:value-of select="descendantScriptCode" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantNoteTypeId">
					<tr>
						<td>Note Type ID:</td>
						<td align="left"><xsl:value-of select="descendantNoteTypeId" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantSources">
					<tr>
						<td>Sources:</td>
						<td align="left"><xsl:value-of select="descendantSources" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantNote">
					<tr>
						<td>Note:</td>
						<td align="left"><xsl:value-of select="descendantNote" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantHistory">
					<tr>
						<td>History:</td>
						<td align="left"><xsl:value-of select="descendantHistory" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantCreatorAuthorizedFormOfNamePerson">
					<tr>
						<td>Authorized Form of Name (Person):</td>
						<td align="left"><xsl:value-of select="descendantCreatorAuthorizedFormOfNamePerson" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantCreatorAuthorizedFormOfNameFamily">
					<tr>
						<td>Authorized Form of Name (Family):</td>
						<td align="left"><xsl:value-of select="descendantCreatorAuthorizedFormOfNameFamily" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantCreatorAuthorizedFormOfNameCorporate">
					<tr>
						<td>Authorized Form of Name (Corporate):</td>
						<td align="left"><xsl:value-of select="descendantCreatorAuthorizedFormOfNameCorporate" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantCreatorAuthorizedFormOfNameOther">
					<tr>
						<td>Authorized Form of Name (Other):</td>
						<td align="left"><xsl:value-of select="descendantCreatorAuthorizedFormOfNameOther" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantCreatorDatesOfExistence">
					<tr>
						<td>Dates of Existence:</td>
						<td align="left"><xsl:value-of select="descendantCreatorDatesOfExistence" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantScopeAndContent">
					<tr>
						<td>Scope and Content:</td>
						<td align="left"><xsl:value-of select="descendantScopeAndContent" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantArrangement">
					<tr>
						<td>Arrangement:</td>
						<td align="left"><xsl:value-of select="descendantArrangement" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantEntityTypeIdFamilyname">
					<tr>
						<td>Entity Type ID (Family):</td>
						<td align="left"><xsl:value-of select="descendantEntityTypeIdFamilyname" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantName">
					<tr>
						<td>Name:</td>
						<td align="left"><xsl:value-of select="descendantName" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantSubjectAccessPoints">
					<tr>
						<td>Subject Access Points:</td>
						<td align="left"><xsl:value-of select="descendantSubjectAccessPoints" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantPlaces">
					<tr>
						<td>Places:</td>
						<td align="left"><xsl:value-of select="descendantPlaces" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantPhysicalCharacteristics">
					<tr>
						<td>Physical Characteristics:</td>
						<td align="left"><xsl:value-of select="descendantPhysicalCharacteristics" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantAppraisal">
					<tr>
						<td>Appraisal:</td>
						<td align="left"><xsl:value-of select="descendantAppraisal" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantAcquisition">
					<tr>
						<td>Acquisition:</td>
						<td align="left"><xsl:value-of select="descendantAcquisition" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantAccruals">
					<tr>
						<td>Accruals:</td>
						<td align="left"><xsl:value-of select="descendantAccruals" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantArchivalHistory">
					<tr>
						<td>Archival History:</td>
						<td align="left"><xsl:value-of select="descendantArchivalHistory" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantRevisionHistory">
					<tr>
						<td>Revision History:</td>
						<td align="left"><xsl:value-of select="descendantRevisionHistory" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantArchivistsNotes">
					<tr>
						<td>Archivists Notes:</td>
						<td align="left"><xsl:value-of select="descendantArchivistsNotes" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantLocationOfOriginals">
					<tr>
						<td>Location of Originals:</td>
						<td align="left"><xsl:value-of select="descendantLocationOfOriginals" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantLocationOfCopies">
					<tr>
						<td>Location of Copies:</td>
						<td align="left"><xsl:value-of select="descendantLocationOfCopies" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantRelatedUnitsOfDescription">
					<tr>
						<td>Related Units of Description:</td>
						<td align="left"><xsl:value-of select="descendantRelatedUnitsOfDescription" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantAccessConditions">
					<tr>
						<td>Access Conditions:</td>
						<td align="left"><xsl:value-of select="descendantAccessConditions" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantReproductionConditions">
					<tr>
						<td>Reproduction Conditions:</td>
						<td align="left"><xsl:value-of select="descendantReproductionConditions" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantFindingAids">
					<tr>
						<td>Finding Aids:</td>
						<td align="left"><xsl:value-of select="descendantFindingAids" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descendantBibliography">
					<tr>
						<td>Bibliography Publication Notes:</td>
						<td align="left"><xsl:value-of select="descendantBibliography" /></td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>		
		<!-- Report details ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
		<p style="page-break-before: always"/>
		<tr>
			<td bordercolor="#7F8B8B" colspan="2"><font color="#FF9933" size="12"><b>Report Details</b></font></td>
		</tr>
		<xsl:choose>
			<xsl:when test="version">
				<tr>
					<td>Version: </td>
					<td align="left"><xsl:value-of select="version" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="generateddate">
				<tr>
					<td>Generated Date: </td>
					<td align="left"><xsl:value-of select="generateddate" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="language">
				<tr>
					<td>Report Language: </td>
					<td align="left"><xsl:value-of select="language" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="rules">
				<tr>
					<td>Rules: </td>
					<td align="left"><xsl:value-of select="rules" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="countrycode">
				<tr>
					<td>Country Code: </td>
					<td align="left"><xsl:value-of select="countrycode" /></td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		
		
	</xsl:template>
</xsl:stylesheet>
