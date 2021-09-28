<div id="profile" class="mt-4">
    <div class="text-center">
        <div class="logo">
            <span class="fa fa-comments"></span>
        </div>
        <br>
        <h4>
            Welcome
            <b>
                <?php echo ucwords($_SESSION['login_name']) ?>
            </b>
            to Collectibles Chat System
        </h4>
    </div>
</div>

<style>
    #profile {
        display: flex;
        height: calc(80%);
        width: calc(100%);
        justify-content: center;
        align-items: center
    }

    #pp {
        max-width: calc(100%);
        max-height: calc(100%);
        border-radius: 100%;
    }

    .img {
        width: 150px;
        height: 150px;
        align-self: center;
        border-radius: 50%;
        border: 3px solid #808080c2;
        display: flex;
        justify-content: center;
        text-align: -webkit-auto;
    }
</style>