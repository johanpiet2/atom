<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output method="xml" indent="yes" doctype-system="user.dtd" />
	<xsl:template match="/user">
		<user>
			<xsl:apply-templates />
		</user>
	</xsl:template>
	<xsl:template match="/useruser">
		<user>
			<xsl:apply-templates />
		</user>
	</xsl:template>
	<xsl:template match="user/userinfo">
		<userinfo>
			<xsl:choose>
				<xsl:when test="username">
					<xsl:if test="username != ''">
						<username>
							<xsl:value-of select="username" />
						</username>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="userId">
					<xsl:if test="userId != ''">
						<userid>
							<xsl:value-of select="userId" />
						</userid>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="userid">
					<xsl:if test="userid != ''">
						<userid>
							<xsl:value-of select="userid" />
						</userid>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="useremail">
					<xsl:if test="useremail != ''">
						<useremail>
							<xsl:value-of select="useremail" />
						</useremail>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="useractive">
					<xsl:if test="useractive != ''">
						<useractive>
							<xsl:value-of select="useractive" />
						</useractive>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise />
			</xsl:choose>
		</userinfo>
	</xsl:template>
</xsl:stylesheet>
