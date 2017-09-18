<h2>Meine Lehrer</h2>
<hr>
<table>
	{foreach $data.teachers as $teacher}
		<tr>
			<td>{$teacher.surname}, {$teacher.name}</td>
			<td><a href="vote.php?teacher={$teacher.id}">Bewerten</a></td>
		</tr>
	{/foreach}
</table>
