addGame.onclick = function ()
{

    let inp = document.querySelector('.field').content;
    let new_inp = inp.cloneNode(true)
    let div = document.getElementById('games');

    div.appendChild(new_inp);
};
removeGame.onclick = function ()
{
    let div = document.getElementById('games');
    let last = div.querySelector('select:last-child');
    div.removeChild(last);
};
