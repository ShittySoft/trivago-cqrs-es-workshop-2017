<?php /* @var \Rhumsaa\Uuid\Uuid $buildId */ ?>
<h1>Welcome to CQRS+ES building</h1>

<h2>Check In: </h2>
<form action="/checkin?id=<?php echo $buildId->toString(); ?>" method="post">
    <select name="username" placeholder="Enter with your username">
        <option selected disabled>-- Choice someone --</option>
        <option value="ocramius">Ocramius</option>
        <option value="malukenho">Malukenho</option>
    </select>

    <button>CheckIn</button>
</form>

<h2>Check Out: </h2>
<form action="/checkout?id=<?php echo $buildId->toString(); ?>" method="post">
    <select name="username" placeholder="Enter with your username">
        <option selected disabled>-- Choice someone --</option>
        <option value="ocramius">Ocramius</option>
        <option value="malukenho">Malukenho</option>
    </select>

    <button>CheckOut</button>
</form>
