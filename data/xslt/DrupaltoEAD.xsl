<?xml version="1.0" encoding="UTF-8"?>
<!-- 
STYLESHEET FOR CONVERSION OF Drupal export to EAD 2002. 
PREVIOUS VERSION 
THIS VERSION     
  
-->
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.1">
	<xsl:output method="xml" version="1.0" omit-xml-declaration="no" indent="yes" encoding="UTF-8" doctype-public="+//ISBN 1-931666-00-8//DTD ead.dtd (Encoded Archival Description (EAD) Version 2002)//EN" doctype-system="ead.dtd" />
	
	<xsl:strip-space elements="*" />
	<!--========================================================================-->
	<!--  USER DEFINED VARIABLES                                                -->
	<!--========================================================================-->
	<!-- All of these variables, XSLT paramaters, may also be overriden from the command line:
     see readme.txt in this distribution
-->
	<xsl:param name="countrycode">za</xsl:param>
	<!-- Added to the <eadid> as @countrycode. Use ISO 3166-1 values.
-->
	<xsl:param name="mainagencycode">ctY</xsl:param>
	<!-- Added to <eadid> as @mainagencycode. Use ISO 15511 values.
     This is the code of the finding aids maintainer, and may not necessaily be the same as
     the repository code.
-->
	<xsl:param name="convdate">July 09, 2015</xsl:param>
	<!-- conversion date 
     default convdate value may be overridden from the command line
     eg., using saxon:  
     saxon -o ead-v2002.xml ead-v1.xml xsl\v1to02.xsl convdate="non-default-value" 
-->
	<xsl:param name="isoconvdate">20150709</xsl:param>
	<!-- conversion date: USE ISO 8601 
     eg., using saxon:  
     saxon -o ead-v2002.xml ead-v1.xml xsl\v1to02.xsl convdate="non-default-value" 
-->
	<xsl:param name="docname">conversion</xsl:param>
	<!-- default docname value may be overridden from the command line
     docname is the name of the document being converted, and is used in indentifing reports
     eg., using saxon:  
     saxon -o ead-v2002.xml ead-v1.xml v1to02.xsl docname="ead-v1.xml"
-->
	<xsl:param name="dtdpath">ead.dtd</xsl:param>
	<!-- path to EAD 2002 dtd. May be local or remote: 
     e.g. file:///c:/ead/dtds/ead.dtd
          http://my.server.com/dtd/ead.dtd
-->
	<xsl:param name="report">n</xsl:param>
	<!--produce a report of the conversion: "y" or "n" 
-->
	<xsl:param name="reportpath">
		<xsl:text>/var/www/prod/uploads/error</xsl:text>
		<xsl:value-of select="$docname" />
		<xsl:text>.report.html</xsl:text>
	</xsl:param>
	<!--location (and name extension) the report should be written to -->
	<xsl:param name="bundle">n</xsl:param>
	<!--replace bundle <adminfo> and <add> within their own <descgrps>s: "y" or "n"
    Stylesheet by default UNBUNDLES the children of <add> and <admininfo>, unless:
    * this parameter is set to 'y', they are bundled with <descgroup type="originalElementName">
    * <admininfo>, <add> have a <head> or other "block" level elements (address, chronlist, list, note, table, p, blockquote)
       they are bundled with <descgroup type="originalElementName">
    * <admininfo>, <add> consist _only_ of "block elements" they are bundles with <odd type="originalElementName">
-->
	<xsl:param name="langlang">eng</xsl:param>
	<!-- Default is English (ISO639-2b 'eng'). Valid alternatives in this stylesheet version are: French(ISO639-2b 'fre') 
     determines whether replacement of @langmaterial atributes in EAD v1.0 with EAD 20002
       <langmaterial>
         <language langcode="value">value</language>
       </langmaterial>
     is written out with English or French language names.
     ISO639-2 language codes (and names in French and English are read from iso639-2.xml)
-->
	<xsl:param name="converter">v1to02.xsl (sy2003-10-15)</xsl:param>
	<!-- name of the conversion script
     Change this value, only if you modify the THIS STYLESHEET
-->
	<!--========================================================================-->
	<!--  END USER DEFINED VARIABLES                                            -->
	<!--========================================================================-->
	<!-- create the report, calling in report.xsl-->
	<xsl:template match="/">
		<xsl:apply-templates select="*|@*|comment()|processing-instruction()|text()" />
		<xsl:if test="$report=&quot;y&quot;">
			<xsl:document method="html" indent="yes" encoding="UTF-8" doctype-public="-//W3C//DTD HTML 4.0//EN" doctype-system="" href="{$reportpath}">
				<xsl:element name="html">
					<xsl:element name="head">
						<xsl:element name="style">
							<xsl:text>body {</xsl:text>
							<xsl:text>margin-top:0.25in;margin-left:0.50in;</xsl:text>
							<xsl:text>margin-right:0.50in;margin-bottom:3.0in;</xsl:text>
							<xsl:text>font-family: century;</xsl:text>
							<xsl:text>}</xsl:text>
						</xsl:element>
					</xsl:element>
					<xsl:element name="body">
						<xsl:element name="div">
							<xsl:element name="h4">
								<xsl:text>Finding aid title:</xsl:text>
								<xsl:value-of select="//titleproper" />
							</xsl:element>
							<xsl:element name="h4">
								<xsl:text>Unit title:</xsl:text>
								<xsl:value-of select="//archdesc/did/unittitle" />
							</xsl:element>
							<xsl:element name="h4">
								<xsl:text>EADID:</xsl:text>
								<xsl:value-of select="//eadid" />
							</xsl:element>
							<xsl:if test="contains(system-property(&quot;xsl:vendor&quot;), &quot;SAXON&quot;)">
								<xsl:element name="h4">
									<xsl:text>Systemid:</xsl:text>
									<xsl:value-of select="saxon:system-id()" />
								</xsl:element>
							</xsl:if>
							<xsl:element name="h4">
								<xsl:text>Converted</xsl:text>
								<xsl:value-of select="$convdate" />
								<xsl:text>using XSLT:</xsl:text>
								<xsl:value-of select="$converter" />
							</xsl:element>
						</xsl:element>
						<xsl:element name="ol">
							<xsl:apply-templates mode="change" select="*|@*" />
						</xsl:element>
					</xsl:element>
				</xsl:element>
			</xsl:document>
		</xsl:if>
	</xsl:template>
	<xsl:template match="/">
		<ead>
			<xsl:apply-templates select="/eadsubmission" />
			<xsl:apply-templates select="eadsubmission/archdesc" />
			<xsl:apply-templates select="/accessionarchival" />
			<xsl:apply-templates select="accessionarchival/accessionarchivaldetail" />
		</ead>
	</xsl:template>
	<xsl:template match="/eadsubmission">
		<eadheader>
			<eadid>
				<xsl:attribute name="identifier">
					<xsl:value-of select="descriptionIdentifier" />
				</xsl:attribute>
				<xsl:attribute name="countrycode">
					<xsl:value-of select="countrycode" />
				</xsl:attribute>
				<xsl:attribute name="mainagencycode">
					<xsl:value-of select="countrycode" />
					-
					<xsl:value-of select="repositorycode" />
				</xsl:attribute>
				<xsl:attribute name="encodinganalog">identifier</xsl:attribute>
				<xsl:value-of select="unitid" />
			</eadid>
			<profiledesc>
				<xsl:choose>
					<xsl:when test="rules">
						<xsl:if test="rules != ''">
							<xsl:attribute name="encodinganalog">3.7.2</xsl:attribute>
							<descrules>
								<xsl:value-of select="rules" />
							</descrules>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="desclanguage">
						<xsl:if test="desclanguage != ''">
							<langusage>
							<xsl:variable name="tokenizeLang">
								<xsl:call-template name="tokenizeLang">
									<xsl:with-param name="text" select="desclanguage"/>
									<xsl:with-param name="separator" select="'|'"/>
									<xsl:with-param name="displayTag" select="'language'"/>
									<xsl:with-param name="langcode" select="'NoCode'"/>
								</xsl:call-template>
							</xsl:variable>
							<xsl:copy-of select="$tokenizeLang"/>
							</langusage>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
			</profiledesc>
		</eadheader>
	</xsl:template>
	<xsl:template match="eadsubmission/archdesc">
		<archdesc>
			<xsl:attribute name="level">
				<xsl:if test="level != ''">
					<xsl:value-of select="level" />
				</xsl:if>
				<xsl:if test="level = ''">Item</xsl:if>
			</xsl:attribute>
			<xsl:attribute name="relatedencoding">ISAD(G)v2</xsl:attribute>
			<did>
				<xsl:choose>
					<xsl:when test="unittitle">
						<xsl:if test="unittitle != ''">
							<unittitle>
								<xsl:value-of select="unittitle" />
							</unittitle>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="unitid">
						<xsl:if test="unitid != ''">
							<unitid>
								<xsl:attribute name="repositorycode">
									<xsl:value-of select="unitid" />
								</xsl:attribute>
								<xsl:value-of select="unitid"/>
							</unitid>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<repository>
					<xsl:choose>
						<xsl:when test="repocorpname">
							<xsl:if test="repocorpname != ''">
									<repocorpname>
										<xsl:value-of select="repocorpname" />
									</repocorpname>
							</xsl:if>
						</xsl:when>
						<xsl:otherwise />
					</xsl:choose>
				<xsl:choose>
					<xsl:when test="corpname">
						<xsl:if test="corpname != ''">
							<origination>
								<xsl:attribute name="encodinganalog">3.2.1</xsl:attribute>
								<xsl:variable name="corpnameFirst" select="text()" /> 
						        <xsl:for-each select="corpname">
									<xsl:choose>
									 <xsl:when test="not(normalize-space(text()) = '') and (position() = 1)">
										<corpname>
											<xsl:value-of select="text()" />
										</corpname>
									 </xsl:when>
									 <xsl:when test="not(normalize-space(text()) = '') and (position() = 2) " >
										<corpname>
											<xsl:value-of select="text()" />
										</corpname>
									 </xsl:when>
									 <xsl:otherwise>
									 </xsl:otherwise>
									</xsl:choose>						        
						        </xsl:for-each>
							</origination>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				</repository>
				<xsl:choose>
					<xsl:when test="unitdate">
						<unitdate>
							<xsl:value-of select="unitdate" />
							<xsl:choose>
								<xsl:when test="startDate">
									<xsl:if test="startDate != ''">
										<xsl:attribute name="normal">
											<xsl:value-of select="startDate" />
											/
											<xsl:value-of select="endDate" />
										</xsl:attribute>
									</xsl:if>
								</xsl:when>
								<xsl:otherwise>0000/</xsl:otherwise>
							</xsl:choose>
						</unitdate>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="extent">
						<xsl:if test="extent != ''">
							<physdesc>
								<extent>
									<xsl:value-of select="extent" />
								</extent>
							</physdesc>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="note">
						<xsl:if test="note != ''">
							<note>
								<xsl:attribute name="type">generalNote</xsl:attribute>
								<xsl:value-of select="note" />
							</note>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="archivistnote">
						<xsl:if test="archivistnote != ''">
							<note>
								<xsl:attribute name="type">sourcesDescription</xsl:attribute>
								<xsl:value-of select="archivistnote" />
							</note>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="desclanguage">
						<xsl:if test="desclanguage != ''">
							<langusage>
								<xsl:attribute name="encodinganalog">3.4.3</xsl:attribute>
								<xsl:variable name="tokenizeLang">
									<xsl:call-template name="tokenizeLang">
										<xsl:with-param name="text" select="desclanguage"/>
										<xsl:with-param name="separator" select="'|'"/>
										<xsl:with-param name="displayTag" select="'language'"/>
										<xsl:with-param name="langcode" select="'NoCode'"/>
									</xsl:call-template>
								</xsl:variable>
								<xsl:copy-of select="$tokenizeLang"/>
							</langusage>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="langcode">
						<xsl:if test="langcode != ''">
							<langusage>
								<xsl:attribute name="encodinganalog">3.4.3</xsl:attribute>
								<xsl:variable name="tokenizeLang">
									<xsl:call-template name="tokenizeLang">
										<xsl:with-param name="text" select="langcode"/>
										<xsl:with-param name="separator" select="'|'"/>
										<xsl:with-param name="displayTag" select="'language'"/>
										<xsl:with-param name="langcode" select="'NoCode'"/>
									</xsl:call-template>
								</xsl:variable>
								<xsl:copy-of select="$tokenizeLang"/>
							</langusage>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="langmaterial">
						<xsl:if test="langmaterial != ''">
							<langmaterial>
							<xsl:attribute name="encodinganalog">3.4.3</xsl:attribute>
							<xsl:variable name="tokenizeLang">
								<xsl:call-template name="tokenizeLang">
									<xsl:with-param name="text" select="langmaterial"/>
									<xsl:with-param name="separator" select="'|'"/>
									<xsl:with-param name="displayTag" select="'language'"/>
									<xsl:with-param name="langcode" select="'NoCode'"/>
								</xsl:call-template>
							</xsl:variable>
							<xsl:copy-of select="$tokenizeLang"/>
							</langmaterial>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="scriptmaterial">
						<xsl:if test="scriptmaterial != ''">
							<langscriptmaterial>
							<xsl:attribute name="encodinganalog">3.4.3</xsl:attribute>
							<xsl:variable name="tokenizeScriptLang">
								<xsl:call-template name="tokenizeScriptLang">
									<xsl:with-param name="text" select="scriptmaterial"/>
									<xsl:with-param name="separator" select="'|'"/>
									<xsl:with-param name="displayTag" select="'language'"/>
									<xsl:with-param name="scriptcode" select="'NoCode'"/>
								</xsl:call-template>
							</xsl:variable>
							<xsl:copy-of select="$tokenizeScriptLang"/>
							</langscriptmaterial>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="scriptcode">
						<xsl:if test="scriptcode != ''">
							<langscriptcode>
							<xsl:attribute name="encodinganalog">3.4.3</xsl:attribute>
							<xsl:variable name="tokenizeScriptLang">
								<xsl:call-template name="tokenizeScriptLang">
									<xsl:with-param name="text" select="scriptcode"/>
									<xsl:with-param name="separator" select="'|'"/>
									<xsl:with-param name="displayTag" select="'language'"/>
									<xsl:with-param name="scriptcode" select="'NoCode'"/>
								</xsl:call-template>
							</xsl:variable>
							<xsl:copy-of select="$tokenizeScriptLang"/>
							</langscriptcode>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="size">
						<xsl:if test="size != ''">
							<mediasize>
								<xsl:value-of select="size" />
							</mediasize>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="type">
						<xsl:if test="type != ''">
							<type>
								<xsl:value-of select="type" />
							</type>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="availabilityId">
						<xsl:if test="availabilityId != ''">
							<availabilityId>
								<xsl:value-of select="availabilityId" />
							</availabilityId>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="corpname">
						<xsl:if test="corpname != ''">
							<origination>
								<xsl:attribute name="encodinganalog">3.2.1</xsl:attribute>
								<xsl:variable name="corpnameFirst" select="text()" /> 
						        <xsl:for-each select="corpname">
									<xsl:choose>
									 <xsl:when test="not(normalize-space(text()) = '') and (position() = 1)">
										<name>
											<xsl:value-of select="text()" />
										</name>
									 </xsl:when>
									 <xsl:when test="not(normalize-space(text()) = '') and (position() = 2) " >
										<name>
											<xsl:value-of select="text()" />
										</name>
									 </xsl:when>
									 <xsl:otherwise>
									 </xsl:otherwise>
									</xsl:choose>						        
						        </xsl:for-each>
							</origination>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="startDate">
						<xsl:if test="startDate != ''">
							<startDate>
								<xsl:value-of select="startDate" />
							</startDate>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="endDate">
						<xsl:if test="endDate != ''">
							<endDate>
								<xsl:value-of select="endDate" />
							</endDate>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
			</did>
			<xsl:choose>
				<xsl:when test="publicationnote">
					<xsl:if test="publicationnote != ''">
						<bibliography>
							<xsl:attribute name="encodinganalog">3.5.4</xsl:attribute>
							<p>
								<xsl:value-of select="publicationnote" />
							</p>
						</bibliography>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descriptionIdentifier">
					<xsl:if test="descriptionIdentifier != ''">
						<odd>
							<xsl:attribute name="type">descriptionIdentifier</xsl:attribute>
							<p>
								<xsl:value-of select="descriptionIdentifier" />
							</p>
						</odd>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="statusDescription">
					<xsl:if test="statusDescription != ''">
						<odd>
							<xsl:attribute name="type">publicationStatus</xsl:attribute>
							<p>
								<xsl:value-of select="statusDescription" />
							</p>
						</odd>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="levelOfDetail">
					<xsl:if test="levelOfDetail != ''">
						<odd>
							<xsl:attribute name="type">levelOfDetail</xsl:attribute>
							<p>
								<xsl:value-of select="levelOfDetail" />
							</p>
						</odd>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>				
			<xsl:choose>
				<xsl:when test="statusDescription">
					<xsl:if test="statusDescription != ''">
						<odd>
							<xsl:attribute name="type">statusDescription</xsl:attribute>
							<p>
								<xsl:value-of select="statusDescription" />
							</p>
						</odd>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="institutionIdentifier">
					<xsl:if test="institutionIdentifier != ''">
						<odd>
							<xsl:attribute name="type">institutionIdentifier</xsl:attribute>
							<p>
								<xsl:value-of select="institutionIdentifier" />
							</p>
						</odd>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="scopecontent">
					<xsl:if test="scopecontent != ''">
						<scopecontent>
							<xsl:attribute name="encodinganalog">3.2.1</xsl:attribute>
							<p>
								<xsl:value-of select="scopecontent" />
							</p>
						</scopecontent>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="custodhist">
					<xsl:if test="custodhist != ''">
						<custodhist>
							<xsl:attribute name="encodinganalog">3.2.3</xsl:attribute>
							<p>
								<xsl:value-of select="custodhist" />
							</p>
						</custodhist>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="arrangement">
					<xsl:if test="arrangement != ''">
						<arrangement>
							<xsl:attribute name="encodinganalog">3.3.4</xsl:attribute>
							<p>
								<xsl:value-of select="arrangement" />
							</p>
						</arrangement>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="appraisal">
					<xsl:if test="appraisal != ''">
						<appraisal>
							<xsl:attribute name="encodinganalog">3.3.2</xsl:attribute>
							<p>
								<xsl:value-of select="appraisal" />
							</p>
						</appraisal>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="accruals">
					<xsl:if test="accruals != ''">
						<accruals>
							<xsl:attribute name="encodinganalog">3.3.3</xsl:attribute>
							<p>
								<xsl:value-of select="accruals" />
							</p>
						</accruals>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="recordtype">
					<xsl:if test="recordtype != ''">
						<recordtype>
							<p>
								<xsl:value-of select="recordtype" />
							</p>
						</recordtype>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="accessionnumber">
					<xsl:if test="accessionnumber != ''">
						<accessionnumber>
							<xsl:value-of select="accessionnumber" />
						</accessionnumber>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="accessionNumber">
					<xsl:if test="accessionNumber != ''">
						<accessionnumber>
							<xsl:value-of select="accessionNumber" />
						</accessionnumber>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="accessionname">
					<xsl:if test="accessionname != ''">
						<accessionname>
							<xsl:value-of select="accessionname" />
						</accessionname>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="classification">
					<xsl:if test="classification != ''">
						<classification>
							<xsl:value-of select="classification" />
						</classification>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<controlaccess>
				<xsl:choose>
					<xsl:when test="corpname">
						<xsl:if test="corpname != ''">
							<xsl:attribute name="encodinganalog">3.2.1</xsl:attribute>
							<xsl:variable name="corpnameFirst" select="text()" /> 
					        <xsl:for-each select="corpname">
								<xsl:choose>
								 <xsl:when test="not(normalize-space(text()) = '') and (position() = 1)">
									<corpname>
										<xsl:value-of select="text()" />
									</corpname>
								 </xsl:when>
								 <xsl:when test="not(normalize-space(text()) = '') and (position() = 2) " >
									<corpname>
										<xsl:value-of select="text()" />
									</corpname>
								 </xsl:when>
								 <xsl:otherwise>
								 </xsl:otherwise>
								</xsl:choose>						        
					        </xsl:for-each>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="name">
						<xsl:if test="name != ''">
							<name>
								<xsl:attribute name="role">subject</xsl:attribute>
								<xsl:value-of select="name" />
							</name>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="subject">
						<xsl:if test="subject != ''">
							<subject>
								<xsl:value-of select="subject" />
							</subject>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="geogname">
						<xsl:if test="geogname != ''">
							<geogname>
								<xsl:value-of select="geogname" />
							</geogname>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
			</controlaccess>
			<xsl:choose>
				<xsl:when test="phystech">
					<xsl:if test="phystech != ''">
						<phystech>
							<xsl:value-of select="phystech" />
						</phystech>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="date">
					<xsl:if test="date != ''">
						<processinfo>
							<xsl:attribute name="era" />
							<xsl:attribute name="calendar" />
							<date>
								<xsl:value-of select="date" />
							</date>
						</processinfo>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="originalsloc">
					<xsl:if test="originalsloc != ''">
						<originalsloc>
							<xsl:attribute name="encodinganalog">3.5.1</xsl:attribute>
							<p>
								<xsl:value-of select="originalsloc" />
							</p>
						</originalsloc>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="altformavail">
					<xsl:if test="altformavail != ''">
						<altformavail>
							<xsl:attribute name="encodinganalog">3.5.2</xsl:attribute>
							<p>
								<xsl:value-of select="altformavail" />
							</p>
						</altformavail>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="relatedmaterial">
					<xsl:if test="relatedmaterial != ''">
						<relatedmaterial>
							<xsl:attribute name="encodinganalog">3.5.3</xsl:attribute>
							<p>
								<xsl:value-of select="relatedmaterial" />
							</p>
						</relatedmaterial>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="accessrestrict">
					<xsl:if test="accessrestrict != ''">
						<accessrestrict>
							<xsl:attribute name="encodinganalog">3.4.1</xsl:attribute>
							<p>
								<xsl:value-of select="accessrestrict" />
							</p>
						</accessrestrict>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="userestrict">
					<xsl:if test="userestrict != ''">
						<userestrict>
							<xsl:attribute name="encodinganalog">3.4.2</xsl:attribute>
							<p>
								<xsl:value-of select="userestrict" />
							</p>
						</userestrict>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="otherfindaid">
					<xsl:if test="otherfindaid != ''">
						<otherfindaid>
							<xsl:attribute name="encodinganalog">3.4.5</xsl:attribute>
							<p>
								<xsl:value-of select="otherfindaid" />
							</p>
						</otherfindaid>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="source">
					<xsl:if test="source != ''">
						<source>
							<xsl:value-of select="source" />
						</source>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="partno">
					<xsl:if test="partno != ''">
						<partno>
							<xsl:value-of select="partno" />
						</partno>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="startDate">
					<xsl:if test="startDate != ''">
						<startDate>
							<xsl:value-of select="startDate" />
						</startDate>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="endDate">
					<xsl:if test="endDate != ''">
						<endDate>
							<xsl:value-of select="endDate" />
						</endDate>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="registers">
					<xsl:if test="registers != ''">
						<registers>
							<xsl:value-of select="registers" />
						</registers>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="volume">
					<xsl:if test="volume != ''">
						<volno>
							<xsl:value-of select="volume" />
						</volno>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="filereference">
					<xsl:if test="filereference != ''">
						<refno>
							<xsl:value-of select="filereference" />
						</refno>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="acqinfo">
					<xsl:if test="acqinfo != ''">
						<acqinfo>
							<xsl:value-of select="acqinfo" />
						</acqinfo>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="size">
					<xsl:if test="size != ''">
						<mediasize>
							<xsl:value-of select="size" />
						</mediasize>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="type">
					<xsl:if test="type != ''">
						<type>
							<xsl:value-of select="type" />
						</type>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="availabilityId">
					<xsl:if test="availabilityId != ''">
						<availabilityId>
							<xsl:value-of select="availabilityId" />
						</availabilityId>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
		</archdesc>
	</xsl:template>
	<xsl:template match="/accessionarchival">
		<eadheader>
			<eadid>
				<xsl:attribute name="identifier">
					<xsl:value-of select="descriptionIdentifier" />
				</xsl:attribute>
				<xsl:attribute name="countrycode">
					<xsl:value-of select="countrycode" />
				</xsl:attribute>
				<xsl:attribute name="mainagencycode">
					<xsl:value-of select="countrycode" />
					-
					<xsl:value-of select="repositorycode" />
				</xsl:attribute>
				<xsl:attribute name="encodinganalog">identifier</xsl:attribute>
				<xsl:value-of select="unitid" />
			</eadid>
			<xsl:choose>
				<xsl:when test="rules">
					<xsl:if test="rules != ''">
						<profiledesc>
							<xsl:attribute name="encodinganalog">3.7.2</xsl:attribute>
							<descrules>
								<xsl:value-of select="rules" />
							</descrules>
						</profiledesc>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
		</eadheader>
	</xsl:template>
	<xsl:template match="accessionarchival/accessionarchivaldetail">
		<archdesc>
			<xsl:attribute name="level">
				<xsl:value-of select="level" />
			</xsl:attribute>
			<xsl:attribute name="relatedencoding">ISAD(G)v2</xsl:attribute>
			<did>
				<xsl:choose>
					<xsl:when test="unittitle">
						<xsl:if test="unittitle != ''">
							<unittitle>
								<xsl:value-of select="unittitle" />
							</unittitle>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="accessionnumber">
						<xsl:if test="accessionnumber != ''">
							<accessionnumber>
								<xsl:value-of select="accessionnumber" />
							</accessionnumber>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="accessionNumber">
						<xsl:if test="accessionNumber != ''">
							<accessionnumber>
								<xsl:value-of select="accessionNumber" />
							</accessionnumber>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="accessionname">
						<xsl:if test="accessionname != ''">
							<accessionname>
								<xsl:value-of select="accessionname" />
							</accessionname>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="unitid">
						<xsl:if test="unitid != ''">
							<unitid>
								<xsl:attribute name="repositorycode">
									<xsl:value-of select="unitid" />
								</xsl:attribute>
								<!--xsl:value-of select="unitid" /-->
							</unitid>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="unitdate">
						<unitdate>
							<xsl:value-of select="unitdate" />
							<xsl:choose>
								<xsl:when test="startDate">
									<xsl:if test="startDate != ''">
										<xsl:attribute name="normal">
											<xsl:value-of select="startDate" />
											/
											<xsl:value-of select="endDate" />
										</xsl:attribute>
									</xsl:if>
								</xsl:when>
								<xsl:otherwise>0000/</xsl:otherwise>
							</xsl:choose>
						</unitdate>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="extent">
						<xsl:if test="extent != ''">
							<physdesc>
								<extent>
									<xsl:value-of select="extent" />
								</extent>
							</physdesc>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="repocorpname">
						<xsl:if test="repocorpname != ''">
							<repository>
								<repocorpname>
									<xsl:value-of select="repocorpname" />
								</repocorpname>
							</repository>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="note">
						<xsl:if test="note != ''">
							<note>
								<xsl:attribute name="type">generalNote</xsl:attribute>
								<xsl:value-of select="note" />
							</note>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="archivistnote">
						<xsl:if test="archivistnote != ''">
							<note>
								<xsl:attribute name="type">sourcesDescription</xsl:attribute>
								<xsl:value-of select="archivistnote" />
							</note>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="desclanguage">
						<xsl:if test="desclanguage != ''">
							<langusage>
								<xsl:attribute name="encodinganalog">3.4.3</xsl:attribute>
								<xsl:variable name="tokenizeLang">
									<xsl:call-template name="tokenizeLang">
										<xsl:with-param name="text" select="desclanguage"/>
										<xsl:with-param name="separator" select="'|'"/>
										<xsl:with-param name="displayTag" select="'language'"/>
										<xsl:with-param name="langcode" select="'NoCode'"/>
									</xsl:call-template>
								</xsl:variable>
								<xsl:copy-of select="$tokenizeLang"/>
							</langusage>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="langcode">
						<xsl:if test="langcode != ''">
							<langusage>
								<xsl:attribute name="encodinganalog">3.4.3</xsl:attribute>
								<xsl:variable name="tokenizeLang">
									<xsl:call-template name="tokenizeLang">
										<xsl:with-param name="text" select="langcode"/>
										<xsl:with-param name="separator" select="'|'"/>
										<xsl:with-param name="displayTag" select="'language'"/>
										<xsl:with-param name="langcode" select="'NoCode'"/>
									</xsl:call-template>
								</xsl:variable>
								<xsl:copy-of select="$tokenizeLang"/>
							</langusage>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="langmaterial">
						<xsl:if test="langmaterial != ''">
							<langmaterial>
								<xsl:attribute name="encodinganalog">3.4.3</xsl:attribute>
								<xsl:variable name="tokenizeLang">
									<xsl:call-template name="tokenizeLang">
										<xsl:with-param name="text" select="langmaterial"/>
										<xsl:with-param name="separator" select="'|'"/>
										<xsl:with-param name="displayTag" select="'language'"/>
										<xsl:with-param name="langcode" select="'NoCode'"/>
									</xsl:call-template>
								</xsl:variable>
								<xsl:copy-of select="$tokenizeLang"/>
							</langmaterial>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="scriptmaterial">
						<xsl:if test="scriptmaterial != ''">
							<langscriptmaterial>
							<xsl:attribute name="encodinganalog">3.4.3</xsl:attribute>
							<xsl:variable name="tokenizeScriptLang">
								<xsl:call-template name="tokenizeScriptLang">
									<xsl:with-param name="text" select="scriptmaterial"/>
									<xsl:with-param name="separator" select="'|'"/>
									<xsl:with-param name="displayTag" select="'language'"/>
									<xsl:with-param name="scriptcode" select="'NoCode'"/>
								</xsl:call-template>
							</xsl:variable>
							<xsl:copy-of select="$tokenizeScriptLang"/>
							</langscriptmaterial>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="scriptcode">
						<xsl:if test="scriptcode != ''">
							<langscriptcode>
							<xsl:attribute name="encodinganalog">3.4.3</xsl:attribute>
							<xsl:variable name="tokenizeScriptLang">
								<xsl:call-template name="tokenizeScriptLang">
									<xsl:with-param name="text" select="scriptcode"/>
									<xsl:with-param name="separator" select="'|'"/>
									<xsl:with-param name="displayTag" select="'language'"/>
									<xsl:with-param name="scriptcode" select="'NoCode'"/>
								</xsl:call-template>
							</xsl:variable>
							<xsl:copy-of select="$tokenizeScriptLang"/>
							</langscriptcode>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="size">
						<xsl:if test="size != ''">
							<mediasize>
								<xsl:value-of select="size" />
							</mediasize>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="type">
						<xsl:if test="type != ''">
							<type>
								<xsl:value-of select="type" />
							</type>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="availabilityId">
						<xsl:if test="availabilityId != ''">
							<availabilityId>
								<xsl:value-of select="availabilityId" />
							</availabilityId>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="corpname">
						<xsl:if test="corpname != ''">
							<xsl:attribute name="encodinganalog">3.2.1</xsl:attribute>
							<xsl:variable name="corpnameFirst" select="text()" /> 
					        <xsl:for-each select="corpname">
								<xsl:choose>
								 <xsl:when test="not(normalize-space(text()) = '') and (position() = 1)">
									<name>
										<xsl:value-of select="text()" />
									</name>
								 </xsl:when>
								 <xsl:when test="not(normalize-space(text()) = '') and (position() = 2) " >
									<name>
										<xsl:value-of select="text()" />
									</name>
								 </xsl:when>
								 <xsl:otherwise>
								 </xsl:otherwise>
								</xsl:choose>						        
					        </xsl:for-each>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
			</did>
			<xsl:choose>
				<xsl:when test="publicationnote">
					<xsl:if test="publicationnote != ''">
						<bibliography>
							<xsl:attribute name="encodinganalog">3.5.4</xsl:attribute>
							<p>
								<xsl:value-of select="publicationnote" />
							</p>
						</bibliography>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="descriptionIdentifier">
					<xsl:if test="descriptionIdentifier != ''">
						<odd>
							<xsl:attribute name="type">descriptionIdentifier</xsl:attribute>
							<p>
								<xsl:value-of select="descriptionIdentifier" />
							</p>
						</odd>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="statusDescription">
					<xsl:if test="statusDescription != ''">
						<odd>
							<xsl:attribute name="type">publicationStatus</xsl:attribute>
							<p>
								<xsl:value-of select="descriptionIdentifier" />
							</p>
						</odd>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="levelOfDetail">
					<xsl:if test="levelOfDetail != ''">
						<odd>
							<xsl:attribute name="type">levelOfDetail</xsl:attribute>
							<p>
								<xsl:value-of select="levelOfDetail" />
							</p>
						</odd>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="statusDescription">
					<xsl:if test="statusDescription != ''">
						<odd>
							<xsl:attribute name="type">statusDescription</xsl:attribute>
							<p>
								<xsl:value-of select="statusDescription" />
							</p>
						</odd>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="institutionIdentifier">
					<xsl:if test="institutionIdentifier != ''">
						<odd>
							<xsl:attribute name="type">institutionIdentifier</xsl:attribute>
							<p>
								<xsl:value-of select="institutionIdentifier" />
							</p>
						</odd>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="scopecontent">
					<xsl:if test="scopecontent != ''">
						<scopecontent>
							<xsl:attribute name="encodinganalog">3.2.1</xsl:attribute>
							<p>
								<xsl:value-of select="scopecontent" />
							</p>
						</scopecontent>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="custodhist">
					<xsl:if test="custodhist != ''">
						<custodhist>
							<xsl:attribute name="encodinganalog">3.2.3</xsl:attribute>
							<p>
								<xsl:value-of select="custodhist" />
							</p>
						</custodhist>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="arrangement">
					<xsl:if test="arrangement != ''">
						<arrangement>
							<xsl:attribute name="encodinganalog">3.3.4</xsl:attribute>
							<p>
								<xsl:value-of select="arrangement" />
							</p>
						</arrangement>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="appraisal">
					<xsl:if test="appraisal != ''">
						<appraisal>
							<xsl:attribute name="encodinganalog">3.3.2</xsl:attribute>
							<p>
								<xsl:value-of select="appraisal" />
							</p>
						</appraisal>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="accruals">
					<xsl:if test="accruals != ''">
						<accruals>
							<xsl:attribute name="encodinganalog">3.3.3</xsl:attribute>
							<p>
								<xsl:value-of select="accruals" />
							</p>
						</accruals>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="recordtype">
					<xsl:if test="recordtype != ''">
						<recordtype>
							<xsl:value-of select="recordtype" />
						</recordtype>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="accessionnumber">
					<xsl:if test="accessionnumber != ''">
						<accessionnumber>
							<xsl:value-of select="accessionnumber" />
						</accessionnumber>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="accessionNumber">
					<xsl:if test="accessionNumber != ''">
						<accessionnumber>
							<xsl:value-of select="accessionNumber" />
						</accessionnumber>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="accessionname">
					<xsl:if test="accessionname != ''">
						<accessionname>
							<xsl:value-of select="accessionname" />
						</accessionname>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="classification">
					<xsl:if test="classification != ''">
						<classification>
							<xsl:value-of select="classification" />
						</classification>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<controlaccess>
				<xsl:choose>
					<xsl:when test="corpname">
						<xsl:if test="corpname != ''">
							<xsl:attribute name="encodinganalog">3.2.1</xsl:attribute>
							<xsl:variable name="corpnameFirst" select="text()" /> 
					        <xsl:for-each select="corpname">
								<xsl:choose>
								 <xsl:when test="not(normalize-space(text()) = '') and (position() = 1)">
									<corpname>
										<xsl:value-of select="text()" />
									</corpname>
								 </xsl:when>
								 <xsl:when test="not(normalize-space(text()) = '') and (position() = 2) " >
									<corpname>
										<xsl:value-of select="text()" />
									</corpname>
								 </xsl:when>
								 <xsl:otherwise>
								 </xsl:otherwise>
								</xsl:choose>						        
					        </xsl:for-each>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="name">
						<xsl:if test="name != ''">
							<name>
								<xsl:attribute name="role">subject</xsl:attribute>
								<xsl:value-of select="name" />
							</name>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="subject">
						<xsl:if test="subject != ''">
							<subject>
								<xsl:value-of select="subject" />
							</subject>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="geogname">
						<xsl:if test="geogname != ''">
							<geogname>
								<xsl:value-of select="geogname" />
							</geogname>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise />
				</xsl:choose>
			</controlaccess>
			<xsl:choose>
				<xsl:when test="phystech">
					<xsl:if test="phystech != ''">
						<phystech>
							<xsl:value-of select="phystech" />
						</phystech>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="acqinfo">
					<xsl:if test="acqinfo != ''">
						<acqinfo>
							<p>
								<xsl:value-of select="acqinfo" />
							</p>
						</acqinfo>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<processinfo>
				<date>
					<xsl:attribute name="era" />
					<xsl:attribute name="calendar" />
					<xsl:choose>
						<xsl:when test="date">
							<xsl:value-of select="date" />
						</xsl:when>
						<xsl:otherwise />
					</xsl:choose>
				</date>
			</processinfo>
			<xsl:choose>
				<xsl:when test="originalsloc">
					<xsl:if test="originalsloc != ''">
						<originalsloc>
							<xsl:attribute name="encodinganalog">3.5.1</xsl:attribute>
							<p>
								<xsl:value-of select="originalsloc" />
							</p>
						</originalsloc>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="altformavail">
					<xsl:if test="altformavail != ''">
						<altformavail>
							<xsl:attribute name="encodinganalog">3.5.2</xsl:attribute>
							<p>
								<xsl:value-of select="altformavail" />
							</p>
						</altformavail>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="relatedmaterial">
					<xsl:if test="relatedmaterial != ''">
						<relatedmaterial>
							<xsl:attribute name="encodinganalog">3.5.3</xsl:attribute>
							<p>
								<xsl:value-of select="relatedmaterial" />
							</p>
						</relatedmaterial>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="accessrestrict">
					<xsl:if test="accessrestrict != ''">
						<accessrestrict>
							<xsl:attribute name="encodinganalog">3.4.1</xsl:attribute>
							<p>
								<xsl:value-of select="accessrestrict" />
							</p>
						</accessrestrict>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="userestrict">
					<xsl:if test="userestrict != ''">
						<userestrict>
							<xsl:attribute name="encodinganalog">3.4.2</xsl:attribute>
							<p>
								<xsl:value-of select="userestrict" />
							</p>
						</userestrict>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="otherfindaid">
					<xsl:if test="otherfindaid != ''">
						<otherfindaid>
							<xsl:attribute name="encodinganalog">3.4.5</xsl:attribute>
							<p>
								<xsl:value-of select="otherfindaid" />
							</p>
						</otherfindaid>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="source">
					<xsl:if test="source != ''">
						<source>
							<xsl:value-of select="source" />
						</source>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="partno">
					<xsl:if test="partno != ''">
						<partno>
							<xsl:value-of select="partno" />
						</partno>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="startDate">
					<xsl:if test="startDate != ''">
						<startDate>
							<xsl:value-of select="startDate" />
						</startDate>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="endDate">
					<xsl:if test="endDate != ''">
						<endDate>
							<xsl:value-of select="endDate" />
						</endDate>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="registers">
					<xsl:if test="registers != ''">
						<registers>
							<xsl:value-of select="registers" />
						</registers>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="volume">
					<xsl:if test="volume != ''">
						<volno>
							<xsl:value-of select="volume" />
						</volno>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="filereference">
					<xsl:if test="filereference != ''">
						<refno>
							<xsl:value-of select="filereference" />
						</refno>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="acqinfo">
					<xsl:if test="acqinfo != ''">
						<acqinfo>
							<xsl:value-of select="acqinfo" />
						</acqinfo>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="size">
					<xsl:if test="size != ''">
						<mediasize>
							<xsl:value-of select="size" />
						</mediasize>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="type">
					<xsl:if test="type != ''">
						<type>
							<xsl:value-of select="type" />
						</type>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="availabilityId">
					<xsl:if test="availabilityId != ''">
						<availabilityId>
							<xsl:value-of select="availabilityId" />
						</availabilityId>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
		</archdesc>
	</xsl:template>
	
	<xsl:template name="tokenize">
		<xsl:param name="text"/>
		<xsl:param name="displayTag"/>
		<xsl:param name="separator" />
		<xsl:choose>
		    <xsl:when test="not(contains($text, $separator))">
		    <xsl:text disable-output-escaping="yes">&lt;</xsl:text><xsl:value-of select="$displayTag"/><xsl:text disable-output-escaping="yes">&gt;</xsl:text>
				<xsl:attribute name="langcode">
	            	<xsl:value-of select="normalize-space($text)"/>
	            </xsl:attribute>
		    <xsl:text disable-output-escaping="yes">&lt;/</xsl:text><xsl:value-of select="$displayTag"/><xsl:text disable-output-escaping="yes">&gt;</xsl:text>
		    </xsl:when>
		    <xsl:otherwise>
				<xsl:text disable-output-escaping="yes">&lt;</xsl:text><xsl:value-of select="$displayTag"/><xsl:text disable-output-escaping="yes">&gt;</xsl:text>
					<xsl:attribute name="langcode">
			            <xsl:value-of select="normalize-space(substring-before($text, $separator))"/>
			        </xsl:attribute>

				<xsl:text disable-output-escaping="yes">&lt;/</xsl:text><xsl:value-of select="$displayTag"/><xsl:text disable-output-escaping="yes">&gt;</xsl:text>
		        <xsl:call-template name="tokenize">
		            <xsl:with-param name="text" select="substring-after($text, $separator)"/>
		            <xsl:with-param name="separator" select="$separator"/>
					<xsl:with-param name="displayTag" select="$displayTag"/>
		        </xsl:call-template>
		    </xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template name="tokenizeLang">
		<xsl:param name="text"/>
		<xsl:param name="separator" />
		<xsl:param name="displayTag"/>
		<xsl:param name="langcode"/>
		<xsl:choose>
		    <xsl:when test="not(contains($text, $separator))">
		    	<language>
					<xsl:attribute name="langcode"><xsl:value-of select="$langcode" /></xsl:attribute>
			    	<xsl:value-of select="normalize-space($text)"/>
				</language>
		    </xsl:when>
		    <xsl:otherwise>
		    	<language>
					<xsl:attribute name="langcode"><xsl:value-of select="$langcode" /></xsl:attribute>
			    	<xsl:value-of select="normalize-space(substring-before($text, $separator))"/>
				</language>
		        <xsl:call-template name="tokenizeLang">
		            <xsl:with-param name="text" select="substring-after($text, $separator)"/>
		            <xsl:with-param name="separator" select="$separator"/>
					<xsl:with-param name="displayTag" select="$displayTag"/>
					<xsl:with-param name="langcode" select="$langcode"/>
		        </xsl:call-template>
		    </xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template name="tokenizeScriptLang">
		<xsl:param name="text"/>
		<xsl:param name="separator" />
		<xsl:param name="displayTag"/>
		<xsl:param name="scriptcode"/>
		<xsl:choose>
		    <xsl:when test="not(contains($text, $separator))">
		    	<language>
					<xsl:attribute name="scriptcode"><xsl:value-of select="$scriptcode" /></xsl:attribute>
			    	<xsl:value-of select="normalize-space($text)"/>
				</language>
		    </xsl:when>
		    <xsl:otherwise>
		    	<language>
					<xsl:attribute name="scriptcode"><xsl:value-of select="$scriptcode" /></xsl:attribute>
			    	<xsl:value-of select="normalize-space(substring-before($text, $separator))"/>
				</language>
		        <xsl:call-template name="tokenizeScriptLang">
		            <xsl:with-param name="text" select="substring-after($text, $separator)"/>
		            <xsl:with-param name="separator" select="$separator"/>
					<xsl:with-param name="displayTag" select="$displayTag"/>
					<xsl:with-param name="scriptcode" select="$scriptcode"/>
		        </xsl:call-template>
		    </xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
</xsl:transform>

