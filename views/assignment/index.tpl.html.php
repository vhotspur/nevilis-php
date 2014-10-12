<h2>%_Your grade_%: {$grade->grade|--}</h2>
{$grade->comment/h||<p>|</p>}

%if $description != ""%
<h2>%_Description_%</h2>
<p>
{$description/h}
</p>
%endif%

%foreach $files $f%
%before%
<h2>%_Files_%</h2>
<form method="post" action="%url($course_id, $assignment_id, 'upload')%" enctype="multipart/form-data" class="files">
%after%
	<fieldset class="usercomment">
	<legend>%_Your comment_%</legend>
	%if $can_upload%
		<textarea name="comment" cols="80" rows="5">{$usercomment/h}</textarea>
	%else%
		<p>{$usercomment/h}</p>
	%endif%
	</fieldset>
	%if $can_upload%
		<input type="submit" value="%_Upload files..._%" />
	%endif%
	</form>
%body%
<fieldset>
	<legend>{$f->name/h}</legend>
	<p>{$f->description/h}</p>
	<p>
		%if $f->submitted%
			<a href="%url($course_id, $assignment_id, $f->filename)%">%_Download your file_%</a>
		%else%
			<i>%_File not yet uploaded._%</i>
		%endif%
	</p>
	
	%if $can_upload%
		<p>
			<input type="file" name="f{$f->afid}" />
		</p>
	%endif%
</fieldset>
%endforeach%
