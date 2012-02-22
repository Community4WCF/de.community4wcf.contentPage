{include file="documentHeader"}
<head>
	<title>{lang}wcf.content.page.page-{@$pageID}{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}
	{include file='imageViewer'}
	<script type="text/javascript">
		//<![CDATA[
		var INLINE_IMAGE_MAX_WIDTH = {@INLINE_IMAGE_MAX_WIDTH}; 
		//]]>
	</script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ImageResizer.class.js"></script>
	{if $polls|isset}<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Poll.class.js"></script>{/if}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">
	
	<ul class="breadCrumbs">
		<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img src="{icon}indexS.png{/icon}" alt="" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
	</ul>
	
	<div class="mainHeadline">
		<img src="{icon}{$page->icon}L.png{/icon}" alt=""/>
		<div class="headlineContainer">
			<h2>{lang}wcf.content.page.page-{@$pageID}{/lang}</h2>
			<p>{lang}wcf.content.page.page-{@$pageID}.shortDescription{/lang}</p>
		</div>
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}
	
	
		<div class="border tabMenuContent titleBarPanel">
			<div class="container-1">
				<div class="messageBody">
					<div id="pollShow" style="width:550pt;">
						{include file='pollShow' polls=$polls pollID=$page->pollID}
					</div>
					<div id="contentPageText{@$pageID}">
						{@$page->getFormattedMessage()}
					</div>
				
					{include file='attachmentsShow' messageID=$page->pageID author=$page->getAuthor()}
				</div>
			
				<div class="buttonBar">
					<div class="smallButtons">
						<ul id="contentPageButtons{@$page->pageID}">
							<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
						
							{if $page->isDeletable()}<li><a href="index.php?action=ContentPageDelete&amp;pageID={@$pageID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" onclick="return confirm('{lang}wcf.content.page.delete.sure{/lang}')" title="{lang}wcf.content.page.delete{/lang}"><img src="{icon}deleteS.png{/icon}" alt="" /> <span>{lang}wcf.global.button.delete{/lang}</span></a></li>{/if}
							{if $page->isEditable()}<li><a href="index.php?form=ContentPageEdit&amp;pageID={@$pageID}{@SID_ARG_2ND}" title="{lang}wcf.content.page.edit{/lang}"><img src="{icon}editS.png{/icon}" alt="" /> <span>{lang}wcf.global.button.edit{/lang}</span></a></li>{/if}
							{if $additionalSmallButtons|isset}{@$additionalSmallButtons}{/if}
						</ul>
					</div>
				</div>
			</div>
		</div>
	
</div>
{include file='footer' sandbox=false}
</body>
</html>

