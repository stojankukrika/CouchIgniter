<?php
$this->load->view('admin/adminHeader');
$this->load->view('admin/adminMenu');
?>
<div>
    <h1>ADMIN VIEW</h1>
<h2>Welcome {myName}</h2>

<br>   All Games:
    <ul id="myGames">
        <li class="bold"><span class="colOne">Creator</span><span class="colOne">Game</span><span class="colTwo">Name</span>
            <span class="colThree">Type</span><span class="colFour">Date</span><span class="colFive">Watch</span><span class="colFive">Delete</span></li>

        <li class="bold" >&nbsp;</li>
    {lobbies}
<li class="{odd}">
        <span class="colOne">{creator}</span>
        <span class="colOne">{gameName}</span>
        <span title="click to change" class="colTwo">{name}</span>
        <span class="colThree {gameType}">{gameType}</span>
        <span class="colFour">{date}</span>
    <a href="<?=site_url("wargame/changeWargame");?>/{id}"><span class="colOne">Watch</span></a>


    <a href="<?=site_url("admin/deleteGame");?>/{id}/">delete</a>
    </li>
{/lobbies}
    </ul>
Games you were invited to:
<ul id="myOtherGames">
    <li class="bold"><span class="colOne">Name</span><span class="colOne">Game</span><span class="colThree">Turn</span><span class="colFour">Date</span><span class="colFive">Players Involved</span></li>
    <li>&nbsp;</li>
    <!--    {otherGames}-->
<!--    <li><a href="--><?//=site_url("wargame/changeWargame");?><!--/{id}/"><span class="colOne">{id}</span><span class="colOne">{name}</span><span class="colTwo">multi</span><span class="colThree {myTurn}">It's {turn} turn.</span><span class="colFour">{date}</span></a><span class="colFive"> {players}</span>-->
<!--    </li>-->
<!--    {/otherGames}-->
</ul>
<a id="logout" href="<?=site_url("users/logout");?>">Logout</a>
    </div>
</body>
</html>