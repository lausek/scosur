<h2>Meine Untertanen</h2>
<hr>
<table>
	{foreach $data.teachers as $teacher}
		<tr>
			<td>{$teacher.surname}, {$teacher.name}</td>
			<td><a href="view.php?teacher={$teacher.id}">Auswerten</a></td>
		</tr>
	{/foreach}
</table>
