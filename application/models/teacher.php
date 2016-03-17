<?php
session_start();

/**
 * Created by PhpStorm.
 * User: Listopadov
 * Date: 14.03.2016
 * Time: 8:40
 */
class teacher
{

    static function addModul($id)
    {
        if (!$id)
            return false;

        $data = R::load('information', $id);

        $typeData = wisdom::getType($data);

//        $subcategory = R::load('category',$wisdomData[3]);
//        $category = R::load('category',$wisdomData[2]);
//        $subtype = R::load('type',$wisdomData[1]);
//        $type = R::load('type',$wisdomData[0]);

        $out = "
                <ol class=\"breadcrumb\">
                    <li><a href=\"?ctrl=wisdom&action=WisdomType&type=" . $typeData[3]->id . "&page=1\" > " . $typeData[3]->name . "</a></li>
                    <li><a href=\"?ctrl=wisdom&action=WisdomType&type=" . $typeData[3]->id . "&subtype=" . $typeData[2]->id . "&page=1\" > " . $typeData[2]->name . "</a></li>
                    <li><a href=\"?ctrl=wisdom&action=WisdomType&type=" . $typeData[3]->id . "&subtype=" . $typeData[2]->id . "&category=" . $typeData[1]->id . "&page=1\" > " . $typeData[1]->name . "</a></li>
                    <li><a href=\"?ctrl=wisdom&action=WisdomType&type=" . $typeData[3]->id . "&subtype=" . $typeData[2]->id . "&category=" . $typeData[1]->id . "&subcategory=" . $typeData[0]->id . "&page=1\" > " . $typeData[0]->name . "</a></li>

                </ol>";


        $out .= "<div class='row'><div class='col-sm-12'><h2>Добавление модуля</h2></div></div>";
        $out .= "<div class='row'><div class='col-sm-6'>
<form method='post' action='?ctrl=teacher&action=AddModul&id=".$id."'>
                <input type='text' name='name' class='form-control' placeholder='Имя'>
                <input type='text' name='number' class='form-control' placeholder='Номер модуля'>
                <h4 class='bg-primary'>Блокировка<small><input type='checkbox' name='block' class='checkbox'></small></h4>
                </div>
                <div class='col-sm-6'>
                <textarea rows='4' class='form-control' name='description' placeholder='Описание'></textarea>
            </div></div> ";

        if ($typeData[3]->id != 1) {
//            echo $typeData[3]->id;
            $out .= "<textarea class='form-control' name='text' placeholder='Материал лекции'></textarea>
                <input type='submit' class='btn btn-info btn-block'></button>
                </form>
        <script src='../../ckeditor/ckeditor.js'></script>
        <script>
        CKEDITOR.replace('text');
        </script>
        ";
        } else {
            $out .= "<input type='submit' class='btn btn-info btn-block'></button></form>";
        }
        return $out;

    }

    static function addWisdom($wisdomData)
    {
        $out = '<h2>Создание учебного материала</h2>';
        db_connect::connect();


        $subcategory = R::load('category', $wisdomData[3]);
        $category = R::load('category', $wisdomData[2]);
        $subtype = R::load('type', $wisdomData[1]);
        $type = R::load('type', $wisdomData[0]);

        $out .= "<div class='col-sm-12'>
                <ol class=\"breadcrumb\">
                    <li><a href=\"?ctrl=wisdom&action=WisdomType&type=" . $type->id . "&page=1\" > " . $type->name . "</a></li>
                    <li><a href=\"?ctrl=wisdom&action=WisdomType&type=" . $type->id . "&subtype=" . $subtype->id . "&page=1\" > " . $subtype->name . "</a></li>
                    <li><a href=\"?ctrl=wisdom&action=WisdomType&type=" . $type->id . "&subtype=" . $subtype->id . "&category=" . $category->id . "&page=1\" > " . $category->name . "</a></li>
                    <li><a href=\"?ctrl=wisdom&action=WisdomType&type=" . $type->id . "&subtype=" . $subtype->id . "&category=" . $category->id . "&subcategory=" . $subcategory->id . "&page=1\" > " . $subcategory->name . "</a></li>

                </ol>";
        $out .= "

            <form method='post' action='?ctrl=teacher&action=WisdomData&category=" . $subcategory->id . "'>
                <input class='form-control' name='name' placeholder='Имя'>



            <textarea class='form-control' name='shortdescription' placeholder='Краткое описание'></textarea>

            <textarea class='form-control' name='description' placeholder='Полное описание'></textarea>
</div>
        <div class='col-sm-6'>
            <input type='reset' class='btn alert-danger btn-block'>
        </div>
        <div class='col-sm-6'>
            <input type='submit' class='btn alert-success btn-block'>
            </form>
        </div>
        <script src='../../ckeditor/ckeditor.js'></script>
        <script>
            CKEDITOR.replace('description');
        </script>
        ";

        return $out;
    }

    static function wisdomRecord($id = 0)
    {
        db_connect::connect();

        if ($id != 0) {
            $information = R::load('information', $id);
        } else {
            $information = R::dispense('information');
        }


        $information->name = $_POST['name'];
        $information->shortdescription = $_POST['shortdescription'];
        $information->category_id = $_GET['category'];
        $information->description = $_POST['description'];

        $information->sharedUser[] = $_SESSION['user'];

        $id = R::store($information);

//        print_r($id);


        return $id;

    }

    static function modulRecord($arr, $id)
    {
        if (!$arr)
            return false;

        $information = R::load('information', $id);
        $typeData = wisdom::getType($information);

        if ($typeData[3]->id == (int)1) {
            $education = R::dispense('education');
            $education->name = $arr['name'];
            $education->description = $arr['description'];
            $education->number = $arr['number'];
//            $education->block = $arr['block'];
            $education->information = $information;

            $id = R::store($education);

        } else {
            $lesson = R::dispense('lesson');
            $lesson->name = $arr['name'];
            $lesson->description = $arr['description'];
            $lesson->number = $arr['number'];
//            $lesson->block = $arr['block'];
            $lesson->text = $arr['text'];
            $lesson->information = $information;

            $id = R::store($lesson);
        }
        return $id;

    }

    static function editWisdom($id)
    {
        $data = R::load('information', $id);
//        print_r($data);
        $typeData = wisdom::getType($data);
//        echo "<br>";
//        print_r($typeData);

//        $subcategory = R::load('category',$wisdomData[3]);
//        $category = R::load('category',$wisdomData[2]);
//        $subtype = R::load('type',$wisdomData[1]);
//        $type = R::load('type',$wisdomData[0]);

        $out = "<li class=\"list-group-item\">
                <ol class=\"breadcrumb\">
                    <li><a href=\"?ctrl=wisdom&action=WisdomType&type=" . $typeData[3]->id . "&page=1\" > " . $typeData[3]->name . "</a></li>
                    <li><a href=\"?ctrl=wisdom&action=WisdomType&type=" . $typeData[3]->id . "&subtype=" . $typeData[2]->id . "&page=1\" > " . $typeData[2]->name . "</a></li>
                    <li><a href=\"?ctrl=wisdom&action=WisdomType&type=" . $typeData[3]->id . "&subtype=" . $typeData[2]->id . "&category=" . $typeData[1]->id . "&page=1\" > " . $typeData[1]->name . "</a></li>
                    <li><a href=\"?ctrl=wisdom&action=WisdomType&type=" . $typeData[3]->id . "&subtype=" . $typeData[2]->id . "&category=" . $typeData[1]->id . "&subcategory=" . $typeData[0]->id . "&page=1\" > " . $typeData[0]->name . "</a></li>

                </ol>";
        $out .= "

            <form method='post' action='?ctrl=teacher&action=WisdomData&category=" . $typeData[0]->id . "&id=" . $id . "'>
                <input class='form-control' name='name' placeholder='Имя' value='" . $data->name . "'>



            <textarea class='form-control' name='shortdescription' placeholder='Краткое описание'>" . $data->shortdescription . "</textarea>

            <textarea class='form-control' name='description' placeholder='Полное описание'>" . $data->description . "</textarea>
</div>
        <div class='col-sm-6'>
            <input type='reset' class='btn alert-danger btn-block'>
        </div>
        <div class='col-sm-6'>
            <input type='submit' class='btn alert-success btn-block'>
            </form>
        </div>
        <script src='../../ckeditor/ckeditor.js'></script>
        <script>
            CKEDITOR.replace('description');
        </script>
        ";

        return $out;
    }

}


//http://obuceisea.my/?ctrl=teacher&action=WisdomData