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

	<xsl:template match="/eadsubmission">
		<ead>
			<xsl:apply-templates />
		</ead>
	</xsl:template>
	
   <xsl:template match="eadsubmission/archdesc">
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
               <descrules>
                  <xsl:attribute name="encodinganalog">3.7.2</xsl:attribute>
                  <xsl:choose>
                     <xsl:when test="rules">
                        <xsl:value-of select="rules" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </descrules>
            </profiledesc>
         </eadheader>
         <archdesc>
            <xsl:attribute name="level">
               <xsl:value-of select="level" />
            </xsl:attribute>
            <xsl:attribute name="relatedencoding">ISAD(G)v2</xsl:attribute>
            <did>
               <xsl:choose>
                  <xsl:when test="unittitle">
                     <unittitle>
                        <xsl:value-of select="unittitle" />
                     </unittitle>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
               <xsl:choose>
                  <xsl:when test="unitid">
                     <unitid>
                        <xsl:attribute name="repositorycode">
                           <xsl:value-of select="repositorycode" />
                        </xsl:attribute>
                        <xsl:value-of select="unitid" />
                     </unitid>
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
               <physdesc>
                  <xsl:choose>
                     <xsl:when test="extent">
                        <extent>
                           <xsl:value-of select="extent" />
                        </extent>
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </physdesc>
               <repository>
                  <xsl:choose>
                     <xsl:when test="corpname">
                        <corpname>
                           <xsl:value-of select="corpname" />
                        </corpname>
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </repository>
               <xsl:choose>
                  <xsl:when test="note">
                     <note>
                        <xsl:attribute name="type">generalNote</xsl:attribute>
                        <xsl:value-of select="note" />
                     </note>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
               <xsl:choose>
                  <xsl:when test="archivistnote">
                     <note>
                        <xsl:attribute name="type">sourcesDescription</xsl:attribute>
                        <xsl:value-of select="archivistnote" />
                     </note>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
               <xsl:choose>
                  <xsl:when test="langmaterial">
                     <langmaterial>
                        <xsl:attribute name="encodinganalog">3.4.3</xsl:attribute>
                        <xsl:value-of select="langmaterial" />
                     </langmaterial>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
               <xsl:choose>
                  <xsl:when test="size">
                     <mediasize>
                        <xsl:value-of select="size" />
                     </mediasize>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
               <xsl:choose>
                  <xsl:when test="type">
                     <type>
                        <xsl:value-of select="type" />
                     </type>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
               <xsl:choose>
                  <xsl:when test="availabilityId">
                     <availabilityId>
                        <xsl:value-of select="availabilityId" />
                     </availabilityId>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </did>
            <bibliography>
               <xsl:attribute name="encodinganalog">3.5.4</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="publicationnote">
                     <p>
                        <xsl:value-of select="publicationnote" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </bibliography>
            <odd>
               <xsl:attribute name="type">descriptionIdentifier</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="descriptionIdentifier">
                     <p>
                        <xsl:value-of select="descriptionIdentifier" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </odd>
            <odd>
               <xsl:attribute name="type">publicationStatus</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="statusDescription">
                     <p>
                        <xsl:value-of select="statusDescription" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </odd>
            <odd>
               <xsl:attribute name="type">levelOfDetail</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="levelOfDetail">
                     <p>
                        <xsl:value-of select="levelOfDetail" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </odd>
            <odd>
               <xsl:attribute name="type">statusDescription</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="statusDescription">
                     <p>
                        <xsl:value-of select="statusDescription" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </odd>
            <odd>
               <xsl:attribute name="type">institutionIdentifier</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="institutionIdentifier">
                     <p>
                        <xsl:value-of select="institutionIdentifier" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </odd>
            <scopecontent>
               <xsl:attribute name="encodinganalog">3.2.1</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="scopecontent">
                     <p>
                        <xsl:value-of select="scopecontent" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </scopecontent>
            <custodhist>
               <xsl:attribute name="encodinganalog">3.2.3</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="custodhist">
                     <p>
                        <xsl:value-of select="custodhist" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </custodhist>
            <custodhist>
               <xsl:attribute name="encodinganalog">3.2.3</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="custodhist">
                     <p>
                        <xsl:value-of select="custodhist" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </custodhist>
            <arrangement>
               <xsl:attribute name="encodinganalog">3.3.4</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="arrangement">
                     <p>
                        <xsl:value-of select="arrangement" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </arrangement>
            <appraisal>
               <xsl:attribute name="encodinganalog">3.3.2</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="appraisal">
                     <p>
                        <xsl:value-of select="appraisal" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </appraisal>
            <accruals>
               <xsl:attribute name="encodinganalog">3.3.3</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="accruals">
                     <p>
                        <xsl:value-of select="accruals" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </accruals>
            <recordtype>
               <xsl:choose>
                  <xsl:when test="recordtype">
                     <p>
                        <xsl:value-of select="recordtype" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </recordtype>
            <classification>
               <xsl:choose>
                  <xsl:when test="classification">
                     <p>
                        <xsl:value-of select="classification" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </classification>
            <controlaccess>
               <corpname>
                  <xsl:choose>
                     <xsl:when test="corpname">
                        <xsl:value-of select="corpname" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </corpname>
               <name>
                  <xsl:attribute name="role">subject</xsl:attribute>
                  <xsl:choose>
                     <xsl:when test="name">
                        <xsl:value-of select="name" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </name>
               <subject>
                  <xsl:choose>
                     <xsl:when test="subject">
                        <xsl:value-of select="subject" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </subject>
               <geogname>
                  <xsl:choose>
                     <xsl:when test="geogname">
                        <xsl:value-of select="geogname" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </geogname>
            </controlaccess>
            <phystech>
               <xsl:choose>
                  <xsl:when test="phystech">
                     <xsl:value-of select="phystech" />
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </phystech>
            <acqinfo>
               <p>
                  <xsl:choose>
                     <xsl:when test="acqinfo">
                        <xsl:value-of select="acqinfo" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </p>
            </acqinfo>
            <acqinfo>
               <p>
                  <xsl:choose>
                     <xsl:when test="acqinfo">
                        <xsl:value-of select="acqinfo" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </p>
            </acqinfo>
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
            <originalsloc>
               <xsl:attribute name="encodinganalog">3.5.1</xsl:attribute>
               <p>
                  <xsl:choose>
                     <xsl:when test="originalsloc">
                        <xsl:value-of select="originalsloc" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </p>
            </originalsloc>
            <altformavail>
               <xsl:attribute name="encodinganalog">3.5.2</xsl:attribute>
               <p>
                  <xsl:choose>
                     <xsl:when test="altformavail">
                        <xsl:value-of select="altformavail" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </p>
            </altformavail>
            <relatedmaterial>
               <xsl:attribute name="encodinganalog">3.5.3</xsl:attribute>
               <p>
                  <xsl:choose>
                     <xsl:when test="relatedmaterial">
                        <xsl:value-of select="relatedmaterial" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </p>
            </relatedmaterial>
            <accessrestrict>
               <xsl:attribute name="encodinganalog">3.4.1</xsl:attribute>
               <p>
                  <xsl:choose>
                     <xsl:when test="accessrestrict">
                        <xsl:value-of select="accessrestrict" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </p>
            </accessrestrict>
            <userestrict>
               <xsl:attribute name="encodinganalog">3.4.2</xsl:attribute>
               <p>
                  <xsl:choose>
                     <xsl:when test="userestrict">
                        <xsl:value-of select="userestrict" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </p>
            </userestrict>
            <otherfindaid>
               <xsl:attribute name="encodinganalog">3.4.5</xsl:attribute>
               <p>
                  <xsl:choose>
                     <xsl:when test="otherfindaid">
                        <xsl:value-of select="otherfindaid" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </p>
            </otherfindaid>
            <source>
               <xsl:choose>
                  <xsl:when test="source">
                     <xsl:value-of select="source" />
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </source>
            <partno>
               <xsl:choose>
                  <xsl:when test="partno">
                     <xsl:value-of select="partno" />
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </partno>
            <volno>
               <xsl:choose>
                  <xsl:when test="volume">
                     <xsl:value-of select="volume" />
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </volno>
            <refno>
               <xsl:choose>
                  <xsl:when test="filereference">
                     <xsl:value-of select="filereference" />
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </refno>
            <acqinfo>
               <xsl:choose>
                  <xsl:when test="acqinfo">
                     <xsl:value-of select="acqinfo" />
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </acqinfo>
            <xsl:choose>
               <xsl:when test="size">
                  <mediasize>
                     <xsl:value-of select="size" />
                  </mediasize>
               </xsl:when>
               <xsl:otherwise />
            </xsl:choose>
            <xsl:choose>
               <xsl:when test="type">
                  <type>
                     <xsl:value-of select="type" />
                  </type>
               </xsl:when>
               <xsl:otherwise />
            </xsl:choose>
            <xsl:choose>
               <xsl:when test="availabilityId">
                  <availabilityId>
                     <xsl:value-of select="availabilityId" />
                  </availabilityId>
               </xsl:when>
               <xsl:otherwise />
            </xsl:choose>
         </archdesc>
   </xsl:template>

	<xsl:template match="/accessionarchival">
		<ead>
			<xsl:apply-templates />
		</ead>
	</xsl:template>
	
   <xsl:template match="accessionarchival/accessionarchivaldetail">
         <eadheader>
            <eadid>
               <xsl:attribute name="identifier">
                  <xsl:value-of select="descriptionIdentifier" />
               </xsl:attribute>
               <xsl:attribute name="countrycode">
                  <xsl:value-of select="countrycode" />
               </xsl:attribute>
               <xsl:attribute name="mainagencycode">
                  <xsl:value-of select="countrycode" />-<xsl:value-of select="repositorycode" />
               </xsl:attribute>
               <xsl:attribute name="encodinganalog">identifier</xsl:attribute>
               <xsl:value-of select="unitid" />
            </eadid>
            <profiledesc>
               <descrules>
                  <xsl:attribute name="encodinganalog">3.7.2</xsl:attribute>
                  <xsl:choose>
                     <xsl:when test="rules">
                        <xsl:value-of select="rules" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </descrules>
            </profiledesc>
         </eadheader>
         <archdesc>
            <xsl:attribute name="level">
               <xsl:value-of select="level" />
            </xsl:attribute>
            <xsl:attribute name="relatedencoding">ISAD(G)v2</xsl:attribute>
            <did>
               <xsl:choose>
                  <xsl:when test="unittitle">
                     <unittitle>
                        <xsl:value-of select="unittitle" />
                     </unittitle>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
               <xsl:choose>
                  <xsl:when test="accessionnumber">
                     <accessionnumber>
                        <xsl:value-of select="accessionnumber" />
                     </accessionnumber>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
               <xsl:choose>
                  <xsl:when test="unitid">
                     <unitid>
                        <xsl:attribute name="repositorycode">
                           <xsl:value-of select="repositorycode" />
                        </xsl:attribute>
                        <xsl:value-of select="unitid" />
                     </unitid>
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
               <physdesc>
                  <xsl:choose>
                     <xsl:when test="extent">
                        <extent>
                           <xsl:value-of select="extent" />
                        </extent>
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </physdesc>
               <repository>
                  <xsl:choose>
                     <xsl:when test="corpname">
                        <corpname>
                           <xsl:value-of select="corpname" />
                        </corpname>
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </repository>
               <xsl:choose>
                  <xsl:when test="note">
                     <note>
                        <xsl:attribute name="type">generalNote</xsl:attribute>
                        <xsl:value-of select="note" />
                     </note>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
               <xsl:choose>
                  <xsl:when test="archivistnote">
                     <note>
                        <xsl:attribute name="type">sourcesDescription</xsl:attribute>
                        <xsl:value-of select="archivistnote" />
                     </note>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
               <xsl:choose>
                  <xsl:when test="langmaterial">
                     <langmaterial>
                        <xsl:attribute name="encodinganalog">3.4.3</xsl:attribute>
                        <xsl:value-of select="langmaterial" />
                     </langmaterial>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
               <xsl:choose>
                  <xsl:when test="size">
                     <mediasize>
                        <xsl:value-of select="size" />
                     </mediasize>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
               <xsl:choose>
                  <xsl:when test="type">
                     <type>
                        <xsl:value-of select="type" />
                     </type>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
               <xsl:choose>
                  <xsl:when test="availabilityId">
                     <availabilityId>
                        <xsl:value-of select="availabilityId" />
                     </availabilityId>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </did>
            <bibliography>
               <xsl:attribute name="encodinganalog">3.5.4</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="publicationnote">
                     <p>
                        <xsl:value-of select="publicationnote" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </bibliography>
            <odd>
               <xsl:attribute name="type">descriptionIdentifier</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="descriptionIdentifier">
                     <p>
                        <xsl:value-of select="descriptionIdentifier" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </odd>
            <odd>
               <xsl:attribute name="type">publicationStatus</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="statusDescription">
                     <p>
                        <xsl:value-of select="statusDescription" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </odd>
            <odd>
               <xsl:attribute name="type">levelOfDetail</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="levelOfDetail">
                     <p>
                        <xsl:value-of select="levelOfDetail" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </odd>
            <odd>
               <xsl:attribute name="type">statusDescription</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="statusDescription">
                     <p>
                        <xsl:value-of select="statusDescription" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </odd>
            <odd>
               <xsl:attribute name="type">institutionIdentifier</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="institutionIdentifier">
                     <p>
                        <xsl:value-of select="institutionIdentifier" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </odd>
            <scopecontent>
               <xsl:attribute name="encodinganalog">3.2.1</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="scopecontent">
                     <p>
                        <xsl:value-of select="scopecontent" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </scopecontent>
            <custodhist>
               <xsl:attribute name="encodinganalog">3.2.3</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="custodhist">
                     <p>
                        <xsl:value-of select="custodhist" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </custodhist>
            <custodhist>
               <xsl:attribute name="encodinganalog">3.2.3</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="custodhist">
                     <p>
                        <xsl:value-of select="custodhist" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </custodhist>
            <arrangement>
               <xsl:attribute name="encodinganalog">3.3.4</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="arrangement">
                     <p>
                        <xsl:value-of select="arrangement" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </arrangement>
            <appraisal>
               <xsl:attribute name="encodinganalog">3.3.2</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="appraisal">
                     <p>
                        <xsl:value-of select="appraisal" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </appraisal>
            <accruals>
               <xsl:attribute name="encodinganalog">3.3.3</xsl:attribute>
               <xsl:choose>
                  <xsl:when test="accruals">
                     <p>
                        <xsl:value-of select="accruals" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </accruals>
            <recordtype>
               <xsl:choose>
                  <xsl:when test="recordtype">
                     <p>
                        <xsl:value-of select="recordtype" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </recordtype>
            <classification>
               <xsl:choose>
                  <xsl:when test="classification">
                     <p>
                        <xsl:value-of select="classification" />
                     </p>
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </classification>
            <controlaccess>
               <corpname>
                  <xsl:choose>
                     <xsl:when test="corpname">
                        <xsl:value-of select="corpname" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </corpname>
               <name>
                  <xsl:attribute name="role">subject</xsl:attribute>
                  <xsl:choose>
                     <xsl:when test="name">
                        <xsl:value-of select="name" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </name>
               <subject>
                  <xsl:choose>
                     <xsl:when test="subject">
                        <xsl:value-of select="subject" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </subject>
               <geogname>
                  <xsl:choose>
                     <xsl:when test="geogname">
                        <xsl:value-of select="geogname" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </geogname>
            </controlaccess>
            <phystech>
               <xsl:choose>
                  <xsl:when test="phystech">
                     <xsl:value-of select="phystech" />
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </phystech>
            <acqinfo>
               <p>
                  <xsl:choose>
                     <xsl:when test="acqinfo">
                        <xsl:value-of select="acqinfo" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </p>
            </acqinfo>
            <acqinfo>
               <p>
                  <xsl:choose>
                     <xsl:when test="acqinfo">
                        <xsl:value-of select="acqinfo" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </p>
            </acqinfo>
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
            <originalsloc>
               <xsl:attribute name="encodinganalog">3.5.1</xsl:attribute>
               <p>
                  <xsl:choose>
                     <xsl:when test="originalsloc">
                        <xsl:value-of select="originalsloc" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </p>
            </originalsloc>
            <altformavail>
               <xsl:attribute name="encodinganalog">3.5.2</xsl:attribute>
               <p>
                  <xsl:choose>
                     <xsl:when test="altformavail">
                        <xsl:value-of select="altformavail" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </p>
            </altformavail>
            <relatedmaterial>
               <xsl:attribute name="encodinganalog">3.5.3</xsl:attribute>
               <p>
                  <xsl:choose>
                     <xsl:when test="relatedmaterial">
                        <xsl:value-of select="relatedmaterial" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </p>
            </relatedmaterial>
            <accessrestrict>
               <xsl:attribute name="encodinganalog">3.4.1</xsl:attribute>
               <p>
                  <xsl:choose>
                     <xsl:when test="accessrestrict">
                        <xsl:value-of select="accessrestrict" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </p>
            </accessrestrict>
            <userestrict>
               <xsl:attribute name="encodinganalog">3.4.2</xsl:attribute>
               <p>
                  <xsl:choose>
                     <xsl:when test="userestrict">
                        <xsl:value-of select="userestrict" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </p>
            </userestrict>
            <otherfindaid>
               <xsl:attribute name="encodinganalog">3.4.5</xsl:attribute>
               <p>
                  <xsl:choose>
                     <xsl:when test="otherfindaid">
                        <xsl:value-of select="otherfindaid" />
                     </xsl:when>
                     <xsl:otherwise />
                  </xsl:choose>
               </p>
            </otherfindaid>
            <source>
               <xsl:choose>
                  <xsl:when test="source">
                     <xsl:value-of select="source" />
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </source>
            <partno>
               <xsl:choose>
                  <xsl:when test="partno">
                     <xsl:value-of select="partno" />
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </partno>
            <volno>
               <xsl:choose>
                  <xsl:when test="volume">
                     <xsl:value-of select="volume" />
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </volno>
            <refno>
               <xsl:choose>
                  <xsl:when test="filereference">
                     <xsl:value-of select="filereference" />
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </refno>
            <acqinfo>
               <xsl:choose>
                  <xsl:when test="acqinfo">
                     <xsl:value-of select="acqinfo" />
                  </xsl:when>
                  <xsl:otherwise />
               </xsl:choose>
            </acqinfo>
            <xsl:choose>
               <xsl:when test="size">
                  <mediasize>
                     <xsl:value-of select="size" />
                  </mediasize>
               </xsl:when>
               <xsl:otherwise />
            </xsl:choose>
            <xsl:choose>
               <xsl:when test="type">
                  <type>
                     <xsl:value-of select="type" />
                  </type>
               </xsl:when>
               <xsl:otherwise />
            </xsl:choose>
            <xsl:choose>
               <xsl:when test="availabilityId">
                  <availabilityId>
                     <xsl:value-of select="availabilityId" />
                  </availabilityId>
               </xsl:when>
               <xsl:otherwise />
            </xsl:choose>
         </archdesc>
   </xsl:template>
</xsl:transform>
