<!-- BEGIN: main -->
<link rel="stylesheet" type="text/css" href="{NV_BASE_SITEURL}themes/{TEMPLATE}/css/block_news_tags.css" />

<div class="news-tags-list">
	<!-- BEGIN: loop -->
	<a href="{LOOP.link}" title="{LOOP.keywords}"><i class="fa fa-tag"></i>{LOOP.keywords}</a><!-- BEGIN: count_tags -->({COUNT})<!-- END: count_tags -->
	<!-- END: loop -->
</div>
<!-- END: main -->