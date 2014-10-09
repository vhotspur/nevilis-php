<h2>%_Your grade_%: {$grade->grade|--}</h2>
%if @$grade->comment != ""%
	<p>{$grade->comment/h}</p>
%endif%

<h2>%_Description_%</h2>
<p>
{$description/h}
</p>

%foreach $files $f%
%before%
<h2>%_Files_%</h2>
<form method="post" action="%url($course_id, $assignment_id, 'upload')%" enctype="multipart/form-data">
%after%
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
