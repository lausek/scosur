<form action="insert.php?teacher={$smarty.get.teacher}" method="POST">
{foreach $data.questions as $q}
	<div class="question">
		<h2>
			{$q.question}
			{if $q.required}
				<font class="important">*</font>
			{/if}
		</h2>
		<div class="answer">
		{if $q.typ == 1}
			<span><input type="radio" name="answer_{$q.id}" value="-2" {if $q.required}required{/if}/> Trifft nicht zu</span>
			<span><input type="radio" name="answer_{$q.id}" value="-1"/> Trifft eher nicht zu</span>
			<span><input type="radio" name="answer_{$q.id}" value="0"/> Unentschieden</span>
			<span><input type="radio" name="answer_{$q.id}" value="1"/> Trifft eher zu</span>
			<span><input type="radio" name="answer_{$q.id}" value="2"/> Trifft zu</span>
		{elseif $q.typ == 2}
			<span><input type="radio" name="answer_{$q.id}" value="1" {if $q.required}required{/if}/> Ja</span>
			<span><input type="radio" name="answer_{$q.id}" value="0"/> Nein</span>
		{elseif $q.typ == 3}
			<input type="text" name="answer_{$q.id}" placeholder="Eingabe..." {if $q.required}required{/if}/>
		{/if}
		</div>
	</div>
{/foreach}
	<div>
		<input type="reset" value="Zurücksetzen" id="reset_button"/>
		<input type="submit" value="Abschicken" id="submit_button"/>
	</div>
	<hr>
	<span class="side_note"><font class="important">*</font> = Mussfeld<span>
</form>

<script>
	(function(){

        function really(part) {
            return function(e) {
                if(!window.confirm("Wirklich "+part+"?")){
    				e.stopPropagation();
    				e.preventDefault();
    			}
            };
        }

		document.getElementById("reset_button").addEventListener("click", really("zurücksetzen"));

		document.getElementById("submit_button").addEventListener("click", really("abschicken"));

	}())
</script>
