<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output method="xml" indent="yes" doctype-system="authority.dtd" />
	<xsl:template match="/authority">
		<authority>
			<xsl:apply-templates />
		</authority>
	</xsl:template>
	<xsl:template match="/donorauthority">
		<authority>
			<xsl:apply-templates />
		</authority>
	</xsl:template>
	<xsl:template match="/legalauthority">
		<authority>
			<xsl:apply-templates />
		</authority>
	</xsl:template>
	<xsl:template match="authority/authorityinfo">
		<authorityinfo>
			<xsl:choose>
				<xsl:when test="targetAuthorizedFormOfName">
					<xsl:if test="targetAuthorizedFormOfName != ''">
						<authorizedFormOfName>
							<xsl:value-of select="targetAuthorizedFormOfName" />
						</authorizedFormOfName>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="authorizedFormOfName">
					<xsl:if test="authorizedFormOfName != ''">
						<authorizedFormOfName>
							<xsl:value-of select="authorizedFormOfName" />
						</authorizedFormOfName>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
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
			<authoritydetail>
				<xsl:choose>
					<xsl:when test="entityTypeId">
						<xsl:if test="entityTypeId != ''">
							<entityTypeId>
								<xsl:value-of select="entityTypeId" />
							</entityTypeId>
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
					<xsl:when test="descRules">
						<xsl:if test="descRules != ''">
							<rules>
								<xsl:value-of select="descRules" />
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
					<xsl:when test="revisionHistory">
						<xsl:if test="revisionHistory != ''">
							<revisionHistory>
								<xsl:value-of select="revisionHistory" />
							</revisionHistory>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="descIdentifier">
						<xsl:if test="descIdentifier != ''">
							<descriptionIdentifier>
								<xsl:value-of select="descIdentifier" />
							</descriptionIdentifier>
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
					<xsl:when test="status">
						<xsl:if test="status != ''">
							<status>
								<xsl:value-of select="status" />
							</status>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="levelOfDetail">
						<xsl:if test="levelOfDetail != ''">
							<levelOfDetail>
								<xsl:value-of select="levelOfDetail" />
							</levelOfDetail>
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
					<xsl:when test="language">
						<xsl:if test="language != ''">
							<language>
								<xsl:value-of select="language" />
							</language>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
			</authoritydetail>
			<authorityaliases>
				<xsl:choose>
					<xsl:when test="parallelFormsOfName">
						<xsl:if test="parallelFormsOfName != ''">
							<parallel>
								<xsl:value-of select="parallelFormsOfName" />
							</parallel>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="otherFormsOfName">
						<xsl:if test="otherFormsOfName != ''">
							<other>
								<xsl:value-of select="otherFormsOfName" />
							</other>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="standardizedFormOfName">
						<xsl:if test="standardizedFormOfName != ''">
							<standardized>
								<xsl:value-of select="standardizedFormOfName" />
							</standardized>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
			</authorityaliases>
			<authorityrelations>
				<xsl:choose>
					<xsl:when test="targetAuthorizedFormOfName">
						<xsl:if test="targetAuthorizedFormOfName != ''">
							<targetAuthorizedFormOfName>
								<xsl:value-of select="targetAuthorizedFormOfName" />
							</targetAuthorizedFormOfName>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relationshipDescription">
						<xsl:if test="relationshipDescription != ''">
							<description>
								<xsl:value-of select="relationshipDescription" />
							</description>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relationshipCategory">
						<xsl:if test="relationshipCategory != ''">
							<category>
								<xsl:value-of select="relationshipCategory" />
							</category>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relationType">
						<xsl:if test="relationType != ''">
							<relationType>
								<xsl:value-of select="relationType" />
							</relationType>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relationDate">
						<xsl:if test="relationDate != ''">
							<date>
								<xsl:value-of select="relationDate" />
							</date>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relationStartDate">
						<xsl:if test="relationStartDate != ''">
							<startDate>
								<xsl:value-of select="relationStartDate" />
							</startDate>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relationEndDate">
						<xsl:if test="relationEndDate != ''">
							<endDate>
								<xsl:value-of select="relationEndDate" />
							</endDate>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>


				<xsl:choose>
					<xsl:when test="relatedTitle">
						<xsl:if test="relatedTitle != ''">
							<relatedTitle>
								<xsl:value-of select="relatedTitle" />
							</relatedTitle>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relatedNature">
						<xsl:if test="relatedNature != ''">
							<relatedNature>
								<xsl:value-of select="relatedNature" />
							</relatedNature>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
						<relatedNature>Creation</relatedNature>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relatedType">
						<xsl:if test="relatedType != ''">
							<relatedType>
								<xsl:value-of select="relatedType" />
							</relatedType>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relatedDate">
						<xsl:if test="relatedDate != ''">
							<relatedDate>
								<xsl:value-of select="relatedDate" />
							</relatedDate>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relatedStartDate">
						<xsl:if test="relatedStartDate != ''">
							<relatedStartDate>
								<xsl:value-of select="relatedStartDate" />
							</relatedStartDate>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relatedEndDate">
						<xsl:if test="relatedEndDate != ''">
							<relatedEndDate>
								<xsl:value-of select="relatedEndDate" />
							</relatedEndDate>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
			</authorityrelations>
		</authorityinfo>
	</xsl:template>
	<xsl:template match="donorauthority/donorauthorityinfo">
		<authorityinfo>
			<xsl:choose>
				<xsl:when test="authorizedFormOfName">
					<authorizedFormOfName>
						<xsl:value-of select="authorizedFormOfName" />
					</authorizedFormOfName>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
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
			<authoritydetail>
				<xsl:choose>
					<xsl:when test="entityTypeId">
						<xsl:if test="entityTypeId != ''">
							<entityTypeId>
								<xsl:value-of select="entityTypeId" />
							</entityTypeId>
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
					<xsl:when test="descRules">
						<xsl:if test="descRules != ''">
							<rules>
								<xsl:value-of select="descRules" />
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
					<xsl:when test="revisionHistory">
						<xsl:if test="revisionHistory != ''">
							<revisionHistory>
								<xsl:value-of select="revisionHistory" />
							</revisionHistory>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="descIdentifier">
						<xsl:if test="descIdentifier != ''">
							<descriptionIdentifier>
								<xsl:value-of select="descIdentifier" />
							</descriptionIdentifier>
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
					<xsl:when test="status">
						<xsl:if test="status != ''">
							<status>
								<xsl:value-of select="status" />
							</status>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="levelOfDetail">
						<xsl:if test="levelOfDetail != ''">
							<levelOfDetail>
								<xsl:value-of select="levelOfDetail" />
							</levelOfDetail>
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
					<xsl:when test="language">
						<xsl:if test="language != ''">
							<language>
								<xsl:value-of select="language" />
							</language>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
			</authoritydetail>
			<authorityaliases>
				<xsl:choose>
					<xsl:when test="parallelFormsOfName">
						<xsl:if test="parallelFormsOfName != ''">
							<parallel>
								<xsl:value-of select="parallelFormsOfName" />
							</parallel>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="otherFormsOfName">
						<xsl:if test="otherFormsOfName != ''">
							<other>
								<xsl:value-of select="otherFormsOfName" />
							</other>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="standardizedFormOfName">
						<xsl:if test="standardizedFormOfName != ''">
							<standardized>
								<xsl:value-of select="standardizedFormOfName" />
							</standardized>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
			</authorityaliases>
			<authorityrelations>
				<xsl:choose>
					<xsl:when test="targetAuthorizedFormOfName">
						<xsl:if test="targetAuthorizedFormOfName != ''">
							<targetAuthorizedFormOfName>
								<xsl:value-of select="targetAuthorizedFormOfName" />
							</targetAuthorizedFormOfName>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relationshipDescription">
						<xsl:if test="relationshipDescription != ''">
							<description>
								<xsl:value-of select="relationshipDescription" />
							</description>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relationshipCategory">
						<xsl:if test="relationshipCategory != ''">
							<category>
								<xsl:value-of select="relationshipCategory" />
							</category>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relationType">
						<xsl:if test="relationType != ''">
							<relationType>
								<xsl:value-of select="relationType" />
							</relationType>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relationDate">
						<xsl:if test="relationDate != ''">
							<date>
								<xsl:value-of select="relationDate" />
							</date>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relationStartDate">
						<xsl:if test="relationStartDate != ''">
							<startDate>
								<xsl:value-of select="relationStartDate" />
							</startDate>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relationEndDate">
						<xsl:if test="relationEndDate != ''">
							<endDate>
								<xsl:value-of select="relationEndDate" />
							</endDate>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>


				<xsl:choose>
					<xsl:when test="relatedTitle">
						<xsl:if test="relatedTitle != ''">
							<relatedTitle>
								<xsl:value-of select="relatedTitle" />
							</relatedTitle>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relatedNature">
						<xsl:if test="relatedNature != ''">
							<relatedNature>
								<xsl:value-of select="relatedNature" />
							</relatedNature>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
						<relatedNature>Creation</relatedNature>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relatedType">
						<xsl:if test="relatedType != ''">
							<relatedType>
								<xsl:value-of select="relatedType" />
							</relatedType>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relatedDate">
						<xsl:if test="relatedDate != ''">
							<relatedDate>
								<xsl:value-of select="relatedDate" />
							</relatedDate>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relatedStartDate">
						<xsl:if test="relatedStartDate != ''">
							<relatedStartDate>
								<xsl:value-of select="relatedStartDate" />
							</relatedStartDate>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relatedEndDate">
						<xsl:if test="relatedEndDate != ''">
							<relatedEndDate>
								<xsl:value-of select="relatedEndDate" />
							</relatedEndDate>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
			</authorityrelations>
		</authorityinfo>
	</xsl:template>
	<xsl:template match="legalauthority/legalauthorityinfo">
		<authorityinfo>
			<xsl:choose>
				<xsl:when test="authorizedFormOfName">
					<xsl:if test="authorizedFormOfName != ''">
						<authorizedFormOfName>
							<xsl:value-of select="authorizedFormOfName" />
						</authorizedFormOfName>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
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
			<authoritydetail>
				<xsl:choose>
					<xsl:when test="entityTypeId">
						<xsl:if test="entityTypeId != ''">
							<entityTypeId>
								<xsl:value-of select="entityTypeId" />
							</entityTypeId>
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
					<xsl:when test="descRules">
						<xsl:if test="descRules != ''">
							<rules>
								<xsl:value-of select="descRules" />
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
					<xsl:when test="revisionHistory">
						<xsl:if test="revisionHistory != ''">
							<revisionHistory>
								<xsl:value-of select="revisionHistory" />
							</revisionHistory>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="descIdentifier">
						<xsl:if test="descIdentifier != ''">
							<descriptionIdentifier>
								<xsl:value-of select="descIdentifier" />
							</descriptionIdentifier>
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
					<xsl:when test="status">
						<xsl:if test="status != ''">
							<status>
								<xsl:value-of select="status" />
							</status>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="levelOfDetail">
						<xsl:if test="levelOfDetail != ''">
							<levelOfDetail>
								<xsl:value-of select="levelOfDetail" />
							</levelOfDetail>
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
					<xsl:when test="language">
						<xsl:if test="language != ''">
							<language>
								<xsl:value-of select="language" />
							</language>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
			</authoritydetail>
			<authorityaliases>
				<xsl:choose>
					<xsl:when test="parallelFormsOfName">
						<xsl:if test="parallelFormsOfName != ''">
							<parallel>
								<xsl:value-of select="parallelFormsOfName" />
							</parallel>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="otherFormsOfName">
						<xsl:if test="otherFormsOfName != ''">
							<other>
								<xsl:value-of select="otherFormsOfName" />
							</other>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="standardizedFormOfName">
						<xsl:if test="standardizedFormOfName != ''">
							<standardized>
								<xsl:value-of select="standardizedFormOfName" />
							</standardized>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
			</authorityaliases>
			<authorityrelations>
				<xsl:choose>
					<xsl:when test="targetAuthorizedFormOfName">
						<xsl:if test="targetAuthorizedFormOfName != ''">
							<targetAuthorizedFormOfName>
								<xsl:value-of select="targetAuthorizedFormOfName" />
							</targetAuthorizedFormOfName>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relationshipDescription">
						<xsl:if test="relationshipDescription != ''">
							<description>
								<xsl:value-of select="relationshipDescription" />
							</description>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relationshipCategory">
						<xsl:if test="relationshipCategory != ''">
							<category>
								<xsl:value-of select="relationshipCategory" />
							</category>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relationType">
						<xsl:if test="relationType != ''">
							<relationType>
								<xsl:value-of select="relationType" />
							</relationType>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relationDate">
						<xsl:if test="relationDate != ''">
							<date>
								<xsl:value-of select="relationDate" />
							</date>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relationStartDate">
						<xsl:if test="relationStartDate != ''">
							<startDate>
								<xsl:value-of select="relationStartDate" />
							</startDate>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relationEndDate">
						<xsl:if test="relationEndDate != ''">
							<endDate>
								<xsl:value-of select="relationEndDate" />
							</endDate>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>


				<xsl:choose>
					<xsl:when test="relatedTitle">
						<xsl:if test="relatedTitle != ''">
							<relatedTitle>
								<xsl:value-of select="relatedTitle" />
							</relatedTitle>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relatedNature">
						<xsl:if test="relatedNature != ''">
							<relatedNature>
								<xsl:value-of select="relatedNature" />
							</relatedNature>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
						<relatedNature>Creation</relatedNature>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relatedType">
						<xsl:if test="relatedType != ''">
							<relatedType>
								<xsl:value-of select="relatedType" />
							</relatedType>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relatedDate">
						<xsl:if test="relatedDate != ''">
							<relatedDate>
								<xsl:value-of select="relatedDate" />
							</relatedDate>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relatedStartDate">
						<xsl:if test="relatedStartDate != ''">
							<relatedStartDate>
								<xsl:value-of select="relatedStartDate" />
							</relatedStartDate>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="relatedEndDate">
						<xsl:if test="relatedEndDate != ''">
							<relatedEndDate>
								<xsl:value-of select="relatedEndDate" />
							</relatedEndDate>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
			</authorityrelations>
		</authorityinfo>
	</xsl:template>
</xsl:stylesheet>
