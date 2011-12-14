<!doctype html>
<html>
    <?=$this->load->view("lobby/lobbyHead.php");?>
    <body>
        <h1>Welcome {user} To the <span style="font-style: italic;">&ldquo;{lobby}&rdquo;"</span></h1>
        {lobbies}
            <a href="<?=site_url("lobby/changeLobby");?>/{id}">{name}</a>
        {/lobbies}
        <a href="<?=site_url("lobby/createLobby");?>">Create Lobby</a>
        <a href="<?=site_url("lobby/logout");?>">logout</a>
        <form onsubmit="doit();return false;" id="chatform" method="post">
            <fieldset style="float:left;">
                <legend>Time
                </legend>
                <div id="clock"></div>
            </fieldset>
            <fieldset style="float:right;">
                <legend>Users
                </legend>
                <div id="users"></div>
            </fieldset>
            <div style="clear:both;"></div>
            <input id="mychat" name="chats" type="text">
            <input name="submit" type="submit">
            <fieldset>
                <legend>Chats
                </legend>
                <div id="chats"></div>
            </fieldset>
            <fieldset>
                <legend>Games
                </legend>
                <div id="games"></div>
            </fieldset>
        </form>
     </body>
</html>