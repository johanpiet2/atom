<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output method="xml" indent="yes" doctype-system="repository.dtd" />
	<xsl:template match="/repository">
		<repository>
			<xsl:apply-templates />
		</repository>
	</xsl:template>
	<xsl:template match="repository/repositoryinfo">
		<repositoryinfo>
			<xsl:choose>
				<xsl:when test="identifier">
					<xsl:if test="identifier != ''">
						<identifier>
							<xsl:value-of select="identifier" />
						</identifier>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="name">
					<xsl:if test="name != ''">
						<authorizedFormOfName>
							<xsl:value-of select="name" />
						</authorizedFormOfName>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<repositorydetail>
				<xsl:choose>
					<xsl:when test="entityTypeId">
						<xsl:if test="entityTypeId != ''">
							<xsl:variable name="tokenize">
								<xsl:call-template name="tokenize">
									<xsl:with-param name="text" select="entityTypeId"/>
									<xsl:with-param name="separator" select="'|'"/>
									<xsl:with-param name="displayTag" select="'entityTypeId'"/>
								</xsl:call-template>
							</xsl:variable>
							<xsl:copy-of select="$tokenize"/>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="history">
						<xsl:if test="history != ''">
							<history>
								<xsl:value-of select="history" />
							</history>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="places">
						<xsl:if test="places != ''">
							<places>
								<xsl:value-of select="places" />
							</places>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="legalStatus">
						<xsl:if test="legalStatus != ''">
							<legalStatus>
								<xsl:value-of select="legalStatus" />
							</legalStatus>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="functions">
						<xsl:if test="functions != ''">
							<functions>
								<xsl:value-of select="functions" />
							</functions>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="mandates">
						<xsl:if test="mandates != ''">
							<mandates>
								<xsl:value-of select="mandates" />
							</mandates>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="internalStructures">
						<xsl:if test="internalStructures != ''">
							<internalStructures>
								<xsl:value-of select="internalStructures" />
							</internalStructures>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="generalContext">
						<xsl:if test="generalContext != ''">
							<generalContext>
								<xsl:value-of select="generalContext" />
							</generalContext>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="rules">
						<xsl:if test="rules != ''">
							<rules>
								<xsl:value-of select="rules" />
							</rules>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="sources">
						<xsl:if test="sources != ''">
							<sources>
								<xsl:value-of select="sources" />
							</sources>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="datesOfExistence">
						<xsl:if test="datesOfExistence != ''">
							<descRevisionHistory>
								<xsl:value-of select="datesOfExistence" />
							</descRevisionHistory>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="descIdentifier">
						<xsl:if test="descIdentifier != ''">
							<descIdentifier>
								<xsl:value-of select="descIdentifier" />
							</descIdentifier>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="corporateBodyIdentifiers">
						<xsl:if test="corporateBodyIdentifiers != ''">
							<corporateBodyIdentifiers>
								<xsl:value-of select="corporateBodyIdentifiers" />
							</corporateBodyIdentifiers>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="openingTimes">
						<xsl:if test="openingTimes != ''">
							<openingTimes>
								<xsl:value-of select="openingTimes" />
							</openingTimes>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="geoculturalContext">
						<xsl:if test="geoculturalContext != ''">
							<geoculturalContext>
								<xsl:value-of select="geoculturalContext" />
							</geoculturalContext>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="holdings">
						<xsl:if test="holdings != ''">
							<holdings>
								<xsl:value-of select="holdings" />
							</holdings>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="findingAids">
						<xsl:if test="findingAids != ''">
							<findingAids>
								<xsl:value-of select="findingAids" />
							</findingAids>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="collectingPolicies">
						<xsl:if test="collectingPolicies != ''">
							<collectingPolicies>
								<xsl:value-of select="collectingPolicies" />
							</collectingPolicies>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="buildings">
						<xsl:if test="buildings != ''">
							<buildings>
								<xsl:value-of select="buildings" />
							</buildings>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="accessConditions">
						<xsl:if test="accessConditions != ''">
							<accessConditions>
								<xsl:value-of select="accessConditions" />
							</accessConditions>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="disabledAccess">
						<xsl:if test="disabledAccess != ''">
							<disabledAccess>
								<xsl:value-of select="disabledAccess" />
							</disabledAccess>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="researchServices">
						<xsl:if test="researchServices != ''">
							<researchServices>
								<xsl:value-of select="researchServices" />
							</researchServices>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="reproductionServices">
						<xsl:if test="reproductionServices != ''">
							<reproductionServices>
								<xsl:value-of select="reproductionServices" />
							</reproductionServices>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="publicFacilities">
						<xsl:if test="publicFacilities != ''">
							<publicFacilities>
								<xsl:value-of select="publicFacilities" />
							</publicFacilities>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="descRules">
						<xsl:if test="descRules != ''">
							<descRules>
								<xsl:value-of select="descRules" />
							</descRules>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="descSources">
						<xsl:if test="descSources != ''">
							<descSources>
								<xsl:value-of select="descSources" />
							</descSources>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="datesOfExistence">
						<xsl:if test="datesOfExistence != ''">
							<datesOfExistence>
								<xsl:value-of select="datesOfExistence" />
							</datesOfExistence>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="descInstitutionIdentifier">
						<xsl:if test="descInstitutionIdentifier != ''">
							<descInstitutionIdentifier>
								<xsl:value-of select="descInstitutionIdentifier" />
							</descInstitutionIdentifier>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="maintenanceNotes">
						<xsl:if test="maintenanceNotes != ''">
							<maintenanceNotes>
								<xsl:value-of select="maintenanceNotes" />
							</maintenanceNotes>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="status">
						<xsl:if test="status != ''">
							<status>
								<xsl:value-of select="status" />
							</status>
						</xsl:if>
						<xsl:if test="status = ''">
							<status>Final</status>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
						<status>Final</status>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="levelOfDetail">
						<xsl:if test="levelOfDetail != ''">
							<levelOfDetail>
								<xsl:value-of select="levelOfDetail" />
							</levelOfDetail>
						</xsl:if>
						<xsl:if test="levelOfDetail = ''">
							<levelOfDetail>Full</levelOfDetail>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
						<levelOfDetail>Full</levelOfDetail>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="uploadLimit">
						<xsl:if test="uploadLimit != ''">
							<uploadLimit>
								<xsl:value-of select="uploadLimit" />
							</uploadLimit>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
						<uploadLimit>-1</uploadLimit>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="thematicArea">
						<xsl:if test="thematicArea != ''">
							<xsl:variable name="tokenize">
								<xsl:call-template name="tokenize">
									<xsl:with-param name="text" select="thematicArea"/>
									<xsl:with-param name="separator" select="'|'"/>
									<xsl:with-param name="displayTag" select="'thematicArea'"/>
								</xsl:call-template>
							</xsl:variable>
							<xsl:copy-of select="$tokenize"/>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
			</repositorydetail>
			<repositoryaliases>
				<xsl:choose>
					<xsl:when test="parallel">
						<xsl:if test="parallel != ''">
							<xsl:variable name="tokenize">
								<xsl:call-template name="tokenize">
									<xsl:with-param name="text" select="parallel"/>
									<xsl:with-param name="separator" select="'|'"/>
									<xsl:with-param name="displayTag" select="'parallel'"/>
								</xsl:call-template>
							</xsl:variable>
							<xsl:copy-of select="$tokenize"/>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				
				<xsl:choose>
					<xsl:when test="other">
						<xsl:if test="other != ''">
							<xsl:variable name="tokenize">
								<xsl:call-template name="tokenize">
									<xsl:with-param name="text" select="other"/>
									<xsl:with-param name="separator" select="'|'"/>
									<xsl:with-param name="displayTag" select="'other'"/>
								</xsl:call-template>
							</xsl:variable>
							<xsl:copy-of select="$tokenize"/>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="authorizedFormOfName">
						<xsl:if test="authorizedFormOfName != ''">
							<xsl:variable name="tokenize">
								<xsl:call-template name="tokenize">
									<xsl:with-param name="text" select="authorizedFormOfName"/>
									<xsl:with-param name="separator" select="'|'"/>
									<xsl:with-param name="displayTag" select="'parallel'"/>
								</xsl:call-template>
							</xsl:variable>
							<xsl:copy-of select="$tokenize"/>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>


			<!--xsl:choose>
				<xsl:when test="authorizedFormOfName">
					<xsl:if test="authorizedFormOfName != ''">
						<authorizedFormOfName>
							<xsl:value-of select="authorizedFormOfName" />
						</authorizedFormOfName>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose-->


			</repositoryaliases>
			<repositoryaddress>
				<xsl:choose>
					<xsl:when test="primarycontact">
						<xsl:if test="primarycontact != ''">
							<primarycontact>
								<xsl:value-of select="primarycontact" />
							</primarycontact>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="contacttype">
						<xsl:if test="contacttype != ''">
							<contacttype>
								<xsl:value-of select="contacttype" />
							</contacttype>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="title">
						<xsl:if test="title != ''">
							<title>
								<xsl:value-of select="normalize-space(title)" /><xsl:text disable-output-escaping="yes">.</xsl:text>
							</title>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="position">
						<xsl:if test="position != ''">
							<position>
								<xsl:value-of select="position" />
							</position>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="contactperson">
						<xsl:if test="contactperson != ''">
							<contactperson>
								<xsl:value-of select="contactperson" />
							</contactperson>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="streetaddress">
						<xsl:if test="streetaddress != ''">
							<streetaddress>
								<xsl:value-of select="streetaddress" />
							</streetaddress>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="city">
						<xsl:if test="city != ''">
							<city>
								<xsl:value-of select="city" />
							</city>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="region">
						<xsl:if test="region != ''">
							<region>
								<xsl:value-of select="region" />
							</region>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="postalcode">
						<xsl:if test="postalcode != ''">
							<postalcode>
								<xsl:value-of select="postalcode" />
							</postalcode>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="telephone">
						<xsl:if test="telephone != ''">
							<telephone>
								<xsl:value-of select="telephone" />
							</telephone>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="website">
						<xsl:if test="website != ''">
							<website>
								<xsl:value-of select="website" />
							</website>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="cellphone">
						<xsl:if test="cellphone != ''">
							<cell>
								<xsl:value-of select="cellphone" />
							</cell>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="fax">
						<xsl:if test="fax != ''">
							<fax>
								<xsl:value-of select="fax" />
							</fax>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="email">
						<xsl:if test="email != ''">
							<email>
								<xsl:value-of select="email" />
							</email>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="postaladdress">
						<xsl:if test="postaladdress != ''">
							<postaladdress>
								<xsl:value-of select="postaladdress" />
							</postaladdress>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="postalregion">
						<xsl:if test="postalregion != ''">
							<postalregion>
								<xsl:value-of select="postalregion" />
							</postalregion>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="postalpostcode">
						<xsl:if test="postalpostcode != ''">
							<postalpostcode>
								<xsl:value-of select="postalpostcode" />
							</postalpostcode>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="postalcity">
						<xsl:if test="postalcity != ''">
							<postalcity>
								<xsl:value-of select="postalcity" />
							</postalcity>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="latitude">
						<xsl:if test="latitude != ''">
							<latitude>
								<xsl:value-of select="latitude" />
							</latitude>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="longitude">
						<xsl:if test="longitude != ''">
							<longitude>
								<xsl:value-of select="longitude" />
							</longitude>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="note">
						<xsl:if test="note != ''">
							<note>
								<xsl:value-of select="note" />
							</note>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
			</repositoryaddress>
		</repositoryinfo>
	</xsl:template>
	
	<xsl:template name="tokenize">
		<xsl:param name="text"/>
		<xsl:param name="displayTag"/>
		<xsl:param name="separator" />
		<xsl:choose>
		    <xsl:when test="not(contains($text, $separator))">
		    <xsl:text disable-output-escaping="yes">&lt;</xsl:text><xsl:value-of select="$displayTag"/><xsl:text disable-output-escaping="yes">&gt;</xsl:text>
		            <xsl:value-of select="normalize-space($text)"/>
		    <xsl:text disable-output-escaping="yes">&lt;/</xsl:text><xsl:value-of select="$displayTag"/><xsl:text disable-output-escaping="yes">&gt;</xsl:text>

		    </xsl:when>
		    <xsl:otherwise>
				<xsl:text disable-output-escaping="yes">&lt;</xsl:text><xsl:value-of select="$displayTag"/><xsl:text disable-output-escaping="yes">&gt;</xsl:text>
		            <xsl:value-of select="normalize-space(substring-before($text, $separator))"/>
				<xsl:text disable-output-escaping="yes">&lt;/</xsl:text><xsl:value-of select="$displayTag"/><xsl:text disable-output-escaping="yes">&gt;</xsl:text>
		        <xsl:call-template name="tokenize">
		            <xsl:with-param name="text" select="substring-after($text, $separator)"/>
		            <xsl:with-param name="separator" select="$separator"/>
					<xsl:with-param name="displayTag" select="$displayTag"/>
		        </xsl:call-template>
		    </xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
</xsl:stylesheet>
