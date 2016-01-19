<!-- BEGIN: main -->
<!-- BEGIN: description -->
<div class="news_column">
	<div class="alert alert-info clearfix">
		<h3>{TITLE}</h3>
		<!-- BEGIN: image -->
		<img alt="{TITLE}" src="{HOMEIMG1}" width="{IMGWIDTH1}" class="img-thumbnail pull-left imghome" />
		<!-- END: image -->
		<p class="text-justify">{DESCRIPTION}</p>
	</div>
</div>
<!-- END: description -->
<!-- BEGIN: topic -->
<div class="news_column panel panel-default">
	<div class="panel-body">
		<!-- BEGIN: homethumb -->
		<div class="videos-thumbnail pull-left col-md-6 col-lg-6 col-xs-6">
			<a href="{TOPIC.link}" title="{TOPIC.title}"><img alt="{TOPIC.alt}" src="{TOPIC.src}" width="{TOPIC.width}" class="img-thumbnail pull-left imghome" /></a>
		</div>
		<!-- END: homethumb -->
		<div class="videos-thumbnail pull-left col-md-18 col-lg-18 col-xs-18">
			<h3><a href="{TOPIC.link}" title="{TOPIC.title}">{TOPIC.title_cut}</a></h3>
			<!-- BEGIN: uploader_link -->
			<p>{LANG.content_uploaded_by}&nbsp;<a href="{TOPIC.uploader_link}" title="{TOPIC.admin_name}">{TOPIC.admin_name}</a></p>
			<!-- END: uploader_link -->
			<!-- BEGIN: uploader -->
			<p>{LANG.content_uploaded_by}&nbsp;{TOPIC.admin_name}</p>
			<!-- END: uploader -->
			<p>
				<span class="pull-left">{TIME}</span>
				<span class="spacer pull-left"></span>
				<span>{TOPIC.hitstotal}&nbsp;{LANG.hits_view}</span>
			</p>
			
			<!-- BEGIN: adminlink -->
			<p class="text-right">
				{ADMINLINK}
			</p>
			<!-- END: adminlink -->
		</div>
	</div>
</div>
<!-- END: topic -->
<!-- BEGIN: other -->
<ul class="related">
	<!-- BEGIN: loop -->
	<li>
		<a title="{TOPIC_OTHER.title}" href="{TOPIC_OTHER.link}">{TOPIC_OTHER.title}</a>
		<em>({TOPIC_OTHER.publtime})</em>
	</li>
	<!-- END: loop -->
</ul>
<!-- END: other -->

<!-- BEGIN: generate_page -->
<div class="text-center">
	{GENERATE_PAGE}
</div>
<!-- END: generate_page -->
<!-- END: main -->