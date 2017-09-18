<center>
  {if isset($data)}
    <span>{$data}</span>
  {/if}
  <form action="index.php" method="POST" id="login_form">
    <input type="text" name="uname" placeholder="Benutzer..."/><br/>
    <input type="password" name="password" placeholder="Passwort..."/><br/>
    <input type="submit" value="Login"/>
  </form>
</center>
