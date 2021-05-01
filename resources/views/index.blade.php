@extends('layout')

@section('title', 'Главная')

@section('page-content')
    <div class="section">
        <div>
            <h2 class="text-primary">
                Расчёт стоимости монтажа компьютерной сети
            </h2>
        </div>
        <div class="py-4">
            <h4>
                С помощью данного калькулятора, Вы сможете расчитать ориентировочную стоимость работ и материалов, необходимых для монтажа локальной сети и сети электропитания в Вашей лаборотории.
            </h4>
        </div>
        <div>
            <form action="#"
                    enctype="multipart/form-data"
                    class="form-inline py-5">

                <div class="form-inline w-100 justify-content-between align-content-center border p-2 my-2">
                    <label class="col-4" for="workplaces">
                        Кол-во рабочих мест
                    </label>
                    <input type="number" name="workplaces" class="col-2 form-control text-center">
                    <span class="col-4">Кол-во рабочих мест расчитывается как сумма сотрудников + кол-во отдельно стоящией техники (принтеры, факсы).</span>
                </div>

                <div class="form-inline w-100 justify-content-between align-content-center border p-2 my-2">
                    <label class="col-4" for="length">
                        Длина лаборатории  <span class="text-muted px-2">в метрах</span>
                    </label>
                    <input type="number" name="length" class="col-2 form-control text-center">
                    <span class="col-4">Укажите длину лаборатории.</span>
                </div>

                <div class="form-inline w-100 justify-content-between align-content-center border p-2 my-2">
                    <label class="col-4" for="width">
                        Ширина лаборатории  <span class="text-muted px-2">в метрах</span>
                    </label>
                    <input type="number" name="width" class="col-2 form-control text-center">
                    <span class="col-4">Укажите ширину лаборатории.</span>
                </div>

                <div class="form-inline w-100 justify-content-between align-content-center border p-2 my-2">
                    <label class="col-4" for="connection">
                        Тип подключения
                    </label>
                    <select name="connection" class="col-2 form-control">
                        <option class="text-center">
                            Категория 5E
                        </option>
                        <option class="text-center">
                            Категория 6
                        </option>
                    </select>
                    <span class="col-4">Категория 5Е - обеспечивает скорость передачи данных на скорости 100Mb/s <br>
                                        Категория 6 - обеспечивает скорость передачи данных на скорости 1000Mb/s.</span>
                </div>

                <div class="form-inline w-100 justify-content-between align-content-center border p-2 my-2">
                    <label class="col-4" for="topology">
                        Тип топологии сети
                    </label>
                    <select name="topology" class="col-2 form-control">
                        <option class="text-center">
                            Топология шины
                        </option>
                        <option class="text-center">
                            Топология звезды
                        </option>
                        <option class="text-center">
                            Кольцевая топология
                        </option>
                        <option class="text-center">
                            Топология дерева
                        </option>
                    </select>
                    <span class="col-4">Топология определяет структуру сети о том, как все компоненты взаимосвязаны друг с другом.</span>
                </div>

            </form>
        </div>
    </div>
@endsection
