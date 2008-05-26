<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:fo="http://www.w3.org/1999/XSL/Format" version="1.0">

	<xsl:import href="http://framework.zend.com/docbook-xsl/html/chunk.xsl" />

	<xsl:param name="use.extensions">0</xsl:param>
	<xsl:param name="use.id.as.filename">1</xsl:param>
	<xsl:param name="base.dir">./</xsl:param>
	<xsl:param name="chunk.fast">0</xsl:param>
	<xsl:param name="make.valid.html">1</xsl:param>
	<xsl:param name="section.autolabel">1</xsl:param>
	<xsl:param name="generate.index">1</xsl:param>
	<xsl:param name="section.label.includes.component.label">1</xsl:param>
	<xsl:param name="sequential.links">1</xsl:param>
	<xsl:param name="chunker.output.indent">yes</xsl:param>
	<xsl:param name="chunker.output.encoding">UTF-8</xsl:param>
	<xsl:param name="chunk.first.sections">0</xsl:param>
	<xsl:param name="chunk.tocs.and.lots">0</xsl:param>
	<xsl:param name="html.extra.head.links">1</xsl:param>
	<xsl:param name="generate.manifest">1</xsl:param>
	<xsl:param name="admon.graphics">1</xsl:param>
	<xsl:param name="admon.style" />
	<xsl:param name="html.stylesheet">styles/docbook.css</xsl:param>
	<xsl:param name="header.rule">0</xsl:param>
	<xsl:param name="footer.rule">0</xsl:param>

	<!-- Start: CHM -->
	<xsl:param name="htmlhelp.chm" select="'zym_framework.chm'" />
	<xsl:param name="htmlhelp.hhc.binary" select="0" />
	<xsl:param name="htmlhelp.hhc.folders.instead.books" select="0" />
	<xsl:param name="toc.section.depth" select="4" />
	<!-- End: CHM -->

	<xsl:template name="user.header.navigation">
		<!-- Stuff put here appears before the top navigation area -->
	</xsl:template>

	<xsl:template name="user.footer.navigation">
		<!-- Stuff put here appears after the bottom navigation area -->
		<xsl:element name="div">
			<xsl:attribute name="class">revinfo</xsl:attribute>
			<xsl:value-of select="//pubdate[1]" />
		</xsl:element>
	</xsl:template>
</xsl:stylesheet>
