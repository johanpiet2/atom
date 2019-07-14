<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output method="xml" indent="yes" doctype-system="researcher.dtd" />
	<xsl:template match="/researcher">
		<researcher>
			<xsl:apply-templates />
		</researcher>
	</xsl:template>
	<xsl:template match="researcher/researcherinfo">
		<researcherinfo>
			<xsl:choose>
				<xsl:when test="researchername">
					<xsl:if test="researchername != ''">
						<researchername>
							<xsl:value-of select="researchername" />
						</researchername>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="researcherid">
					<xsl:if test="researcherid != ''">
						<researcherid>
							<xsl:value-of select="researcherid" />
						</researcherid>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<researcheraddress>
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
								<xsl:value-of select="title" />
								<xsl:text>.</xsl:text>
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
			</researcheraddress>
		</researcherinfo>
	</xsl:template>
</xsl:stylesheet>
