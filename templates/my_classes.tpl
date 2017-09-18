<h2>Meine Klassen</h2>
<hr>
<table>
    {foreach $data.classes as $class}
        <tr>
            <td>{$class.name_short}</td>
            <td>{$class.name_long}</td>
            <td><a href="view.php?class={$class.id}">Auswerten</a></td>
        </tr>
    {/foreach}
</table>
