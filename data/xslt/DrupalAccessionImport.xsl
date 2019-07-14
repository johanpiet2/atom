<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output method="xml" indent="yes" doctype-system="accession.dtd" />
	<xsl:template match="/accession">
		<accession>
			<xsl:apply-templates />
		</accession>
	</xsl:template>
	
	<xsl:template match="accession/accessioninfo">
		<accessioninfo>
			<xsl:choose>
				<xsl:when test="disposalAuthorityNumber">
					<accessionnumber>
						<xsl:value-of select="disposalAuthorityNumber" />
					</accessionnumber>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="acquisitiondate">
					<xsl:if test="acquisitiondate != ''">
						<acquisitiondate>
							<xsl:value-of select="acquisitiondate" />
						</acquisitiondate>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			<accessiondetail>
				<xsl:choose>
					<xsl:when test="title">
						<title>
							<xsl:value-of select="title" />
						</title>
					</xsl:when>
					<xsl:otherwise>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="creatorsid">
						<xsl:if test="creatorsid != ''">
							<creatorsid>
								<xsl:value-of select="creatorsid" />
							</creatorsid>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="donorid">
						<xsl:if test="donorid != ''">
							<donorid>
								<xsl:value-of select="donorid" />
							</donorid>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="acquisitiondate">
						<xsl:if test="acquisitiondate != ''">
							<acquisitiondate>
								<xsl:value-of select="acquisitiondate" />
							</acquisitiondate>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="acquisitiontype">
						<xsl:if test="acquisitiontype != ''">
							<acquisitiontype>
								<xsl:value-of select="acquisitiontype" />
							</acquisitiontype>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="appraisal">
						<xsl:if test="appraisal != ''">
							<appraisal>
								<xsl:value-of select="appraisal" />
							</appraisal>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="history">
						<xsl:if test="history != ''">
							<history>
								<xsl:value-of select="history" />
							</history>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="sourceofacquisition">
						<xsl:if test="sourceofacquisition != ''">
							<sourceofacquisition>
								<xsl:value-of select="sourceofacquisition" />
							</sourceofacquisition>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="locationinformation">
						<xsl:if test="locationinformation != ''">
							<locationinformation>
								<xsl:value-of select="locationinformation" />
							</locationinformation>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="physicalcharacteristics">
						<xsl:if test="physicalcharacteristics != ''">
							<physicalcharacteristics>
								<xsl:value-of select="physicalcharacteristics" />
							</physicalcharacteristics>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="processingnotes">
						<xsl:if test="processingnotes != ''">
							<processingnotes>
								<xsl:value-of select="processingnotes" />
							</processingnotes>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="processingpriority">
						<xsl:if test="processingpriority != ''">
							<processingpriority>
								<xsl:value-of select="processingpriority" />
							</processingpriority>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="processingstatus">
						<xsl:if test="processingstatus != ''">
							<processingstatus>
								<xsl:value-of select="processingstatus" />
							</processingstatus>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="receivedextentunits">
						<xsl:if test="receivedextentunits != ''">
							<receivedextentunits>
								<xsl:value-of select="receivedextentunits" />
							</receivedextentunits>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="resourcetype">
						<xsl:if test="resourcetype != ''">
							<resourcetype>
								<xsl:value-of select="resourcetype" />
							</resourcetype>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="scopeandcontent">
						<xsl:if test="scopeandcontent != ''">
							<scopeandcontent>
								<xsl:value-of select="scopeandcontent" />
							</scopeandcontent>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="culture">
						<xsl:if test="culture != ''">
							<culture>
								<xsl:value-of select="culture" />
							</culture>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
						<culture>en</culture>
					</xsl:otherwise>
				</xsl:choose>
			</accessiondetail>	
		</accessioninfo>
		
	</xsl:template>
</xsl:stylesheet>
