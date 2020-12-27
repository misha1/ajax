<html>
<head>
    <script src="jquery.min.js"></script>
</head>
<body>
<form method="post" action="#">
    <input type="text" name="name" class="nameField" placeholder="Введите имя">
    <input type="text" name="surname" class="surnameField" placeholder="Введите фамилию">
    <input type="text" name="age" class="ageField" placeholder="Введите возраст">
    <input type="submit" value="отправить" class="button">
</form>

<form action="quickstart.php">
    <input type="submit" value="выгрузить" class="button2">
</form>

<table class="rows">

</table>


<script>
    jQuery(document).ready(function() {
        jQuery(".button").bind("click", function(event) {

            event.preventDefault(); // отменяем событие по умолчанию
            if ( validateForm() ) { // если есть ошибки возвращает true
                return false; // прерываем выполнение скрипта
            }


            var name = jQuery('.nameField').val();
            var surname = jQuery('.surnameField').val();
            var age = jQuery('.ageField').val();


            jQuery.ajax({
                url: "for_db.php",
                type: "POST",
                data: {name:name, surname:surname, age: age}, // Передаем данные для записи
                dataType: "json",
                success: function(result) {
                    if (result){
                        jQuery('.rows tr').remove();
                        jQuery('.rows').append(function(){
                            var res = '';
                            for(var i = 0; i < result.users.name.length; i++){
                                res += '<tr><td>' + result.users.id[i] + '</td><td>' + result.users.name[i] + '</td><td>' + result.users.surname[i] + '</td><td>' + result.users.age[i] + '</td></tr>';
                            }
                            return res;
                        });
                        console.log(result);
                    }else{
                        alert(result.message);
                    }
                    return false;
                }
            });

            function validateForm() {
                $(".text-error").remove();

                // Проверка имени
                var el_n    = $(".nameField");
                if (  !(/^[а-яА-ЯёЁa-zA-Z]+$/.test(el_n.val())) ) {
                    var v_name = true;
                    el_n.after('<span class="text-error">Ошибка! Введите имя</span>');
                }
                $(".nameField").toggleClass('error', v_name );

                // Проверка Фамилии
                var el_s    = $(".surnameField");
                if (  !(/^[а-яА-ЯёЁa-zA-Z]+$/.test(el_s.val())) ) {
                    var v_surname = true;
                    el_s.after('<span class="text-error">Ошибка! Введите фамилию</span>');
                }
                $(".surnameField").toggleClass('error', v_surname );

                // Проверка возраста
                var el_a    = $(".ageField");
                if (  !(/^[0-9]+$/.test(el_a.val())) || el_a.val().length > 2 ) {
                    var v_age = true;
                    el_a.after('<span class="text-error">Ошибка! Введите возраст</span>');
                }
                $(".ageField").toggleClass('error', v_age);


                return ( v_name || v_surname || v_age);
            }


        });
    });
</script>
</body>
</html>