class ArduinoComponent {
    constructor(x, y) {
        this.x = x;
        this.y = y;
        this.state = false;
        this.pins = [];
        this.width = 40;
        this.height = 40;
    }

    draw(p) {
        // Базовый метод отрисовки
    }

    update() {
        // Базовый метод обновления состояния
    }

    // Проверка попадания по пину компонента
    getPinAt(x, y) {
        return this.pins.find(pin => {
            const d = Math.sqrt(Math.pow(x - (this.x + pin.offsetX), 2) + 
                              Math.pow(y - (this.y + pin.offsetY), 2));
            return d < 5;
        });
    }

    // Обновление позиций пинов при перемещении компонента
    updatePinPositions() {
        this.pins.forEach(pin => {
            pin.x = this.x + pin.offsetX;
            pin.y = this.y + pin.offsetY;
        });
    }
}

class LED extends ArduinoComponent {
    constructor(x, y) {
        super(x, y);
        this.state = false;
        this.pins = [
            { name: 'GND', type: 'gnd', offsetX: -10, offsetY: 20, connected: false },
            { name: 'VCC', type: 'digital', offsetX: 10, offsetY: 20, connected: false }
        ];
        this.updatePinPositions();
    }

    update(boardState) {
        const digitalPin = this.pins.find(p => p.type === 'digital');
        if (digitalPin && digitalPin.connectedTo) {
            const pinNumber = digitalPin.connectedTo.number;
            if (pinNumber !== undefined) {
                const newState = boardState[pinNumber] === 1;
                if (this.state !== newState) {
                    this.state = newState;
                    console.log(`LED state changed to: ${this.state}`);
                }
            }
        }
    }

    draw(p) {
        p.push();
        p.translate(this.x, this.y);

        // Рисуем ножки
        p.stroke(100);
        p.strokeWeight(2);
        p.line(-10, 0, -10, 20);  // GND
        p.line(10, 0, 10, 20);   // VCC

        // Основной корпус светодиода (прозрачный пластик)
        p.noStroke();
        p.fill(220, 220, 220, 200);
        p.ellipse(0, 0, 20, 20);

        if (this.state) {
            // Включенное состояние - желтый
            
            // Внешнее свечение
            p.fill(255, 255, 0, 50);
            p.ellipse(0, 0, 30, 30);
            
            // Яркое желтое свечение
            p.fill(255, 255, 0);
            p.ellipse(0, 0, 16, 16);
            
            // Внутреннее яркое свечение
            p.fill(255, 255, 200);
            p.ellipse(0, 0, 12, 12);
            
            // Яркий блик
            p.fill(255, 255, 255, 230);
            p.ellipse(-4, -4, 6, 6);
        } else {
            // Выключенное состояние - серый
            
            // Тёмно-серый внутренний круг
            p.fill(80, 80, 80);
            p.ellipse(0, 0, 16, 16);
            
            // Матовая поверхность
            p.fill(120, 120, 120);
            p.ellipse(0, 0, 12, 12);
            
            // Тусклый блик
            p.fill(150, 150, 150, 150);
            p.ellipse(-4, -4, 6, 6);
        }

        // Рисуем пины с подписями
        this.pins.forEach(pin => {
            // Квадратики пинов
            p.stroke(0);
            p.strokeWeight(1);
            p.fill(pin.connected ? '#FFD700' : '#A0A0A0');
            p.rect(pin.offsetX - 3, pin.offsetY - 3, 6, 6);
            
            // Подписи пинов
            p.noStroke();
            p.fill(0);
            p.textSize(12);
            p.textAlign(p.CENTER);
            p.text(pin.name, pin.offsetX, pin.offsetY + 15);
        });

        p.pop();
    }

    getPinAt(x, y) {
        const relX = x - this.x;
        const relY = y - this.y;
        
        for (const pin of this.pins) {
            const d = Math.sqrt(
                Math.pow(relX - pin.offsetX, 2) + 
                Math.pow(relY - pin.offsetY, 2)
            );
            if (d < 5) {
                return pin;
            }
        }
        return null;
    }
}

class Button extends ArduinoComponent {
    constructor(x, y) {
        super(x, y);
        this.pressed = false;
        this.pins = [
            { name: 'GND', type: 'gnd', offsetX: -15, offsetY: 20, connected: false },
            { name: 'VCC', type: 'digital', offsetX: 15, offsetY: 20, connected: false }
        ];
        this.updatePinPositions();
    }

    draw(p) {
        this.updatePinPositions();
        p.push();
        p.translate(this.x, this.y);

        // Рисуем основание кнопки
        p.fill(50);
        p.rect(-20, -5, 40, 10, 2);

        // Рисуем кнопку
        p.fill(this.pressed ? 150 : 200);
        p.rect(-15, -15, 30, this.pressed ? 12 : 15, 5, 5, 2, 2);
        
        // Добавляем тень
        if (!this.pressed) {
            p.fill(100, 100, 100, 50);
            p.rect(-13, -13, 26, 3, 1);
        }

        // Добавляем блик
        p.fill(255, 255, 255, 50);
        p.rect(-12, -12, 24, 4, 2);

        // Рисуем пины
        this.pins.forEach(pin => {
            // Рисуем металлические ножки
            p.stroke(180);
            p.strokeWeight(2);
            p.line(pin.offsetX, 0, pin.offsetX, pin.offsetY);
            
            // Рисуем контакты
            p.fill(pin.connected ? '#FFD700' : '#C0C0C0');
            p.stroke(0);
            p.strokeWeight(1);
            p.rect(pin.offsetX - 3, pin.offsetY - 3, 6, 6);
            
            // Подписи
            p.noStroke();
            p.fill(0);
            p.textSize(10);
            p.textAlign(p.CENTER);
            p.text(pin.name, pin.offsetX, pin.offsetY + 15);
        });

        p.pop();
    }

    checkPress(p, mx, my) {
        let d = p.dist(mx, my, this.x, this.y);
        this.pressed = d < 20;
        return this.pressed;
    }
}

class Potentiometer extends ArduinoComponent {
    constructor(x, y) {
        super(x, y);
        this.value = 0;
        this.angle = 0;
        this.pins = [
            { name: 'GND', type: 'gnd', offsetX: -20, offsetY: 20, connected: false },
            { name: 'VCC', type: 'power', offsetX: 0, offsetY: 20, connected: false },
            { name: 'SIG', type: 'analog', offsetX: 20, offsetY: 20, connected: false }
        ];
        this.updatePinPositions();
    }

    draw(p) {
        this.updatePinPositions();
        p.push();
        p.translate(this.x, this.y);

        // Рисуем основание
        p.fill(30);
        p.rect(-25, -25, 50, 50, 5);

        // Рисуем круговую шкалу
        p.noFill();
        p.stroke(200);
        p.arc(0, 0, 40, 40, -p.PI * 0.75, p.PI * 0.75);

        // Рисуем ручку
        p.push();
        p.rotate(this.angle);
        // Основание ручки
        p.fill(80);
        p.noStroke();
        p.ellipse(0, 0, 30, 30);
        // Индикатор положения
        p.fill(200);
        p.rect(-2, -12, 4, 12);
        // Текстура ручки
        for (let i = 0; i < 6; i++) {
            p.stroke(60);
            p.arc(0, 0, 25, 25, i * p.PI/3, (i * p.PI/3) + p.PI/6);
        }
        p.pop();

        // Рисуем пины
        this.pins.forEach(pin => {
            // Рисуем металлические ножки
            p.stroke(180);
            p.strokeWeight(2);
            p.line(pin.offsetX, 10, pin.offsetX, pin.offsetY);
            
            // Рисуем контакты
            p.fill(pin.connected ? '#FFD700' : '#C0C0C0');
            p.stroke(0);
            p.strokeWeight(1);
            p.rect(pin.offsetX - 3, pin.offsetY - 3, 6, 6);
            
            // Подписи
            p.noStroke();
            p.fill(0);
            p.textSize(10);
            p.textAlign(p.CENTER);
            p.text(pin.name, pin.offsetX, pin.offsetY + 15);
        });

        p.pop();
    }

    update(mx, my) {
        let angle = Math.atan2(my - this.y, mx - this.x);
        this.angle = angle;
        this.value = Math.floor(((angle + Math.PI) / (2 * Math.PI)) * 1023);
    }
}

class LCD extends ArduinoComponent {
    constructor(x, y) {
        super(x, y);
        this.width = 160;
        this.height = 80;
        this.text = ["Hello", "World"];
        this.pins = [
            { name: 'GND', type: 'gnd', offsetX: -70, offsetY: 45, connected: false },
            { name: 'VCC', type: 'power', offsetX: -50, offsetY: 45, connected: false },
            { name: 'SDA', type: 'digital', offsetX: -30, offsetY: 45, connected: false },
            { name: 'SCL', type: 'digital', offsetX: -10, offsetY: 45, connected: false }
        ];
        this.updatePinPositions();
    }

    draw(p) {
        this.updatePinPositions();
        p.push();
        p.translate(this.x, this.y);

        // Рисуем корпус
        p.fill(0, 50, 100);
        p.rect(-this.width/2, -this.height/2, this.width, this.height, 5);

        // Рисуем экран
        p.fill(0, 150, 0, 50);
        p.rect(-this.width/2 + 10, -this.height/2 + 10, 
               this.width - 20, this.height - 20);

        // Рисуем текст
        p.fill(0, 255, 0);
        p.textSize(14);
        p.textAlign(p.LEFT, p.TOP);
        this.text.forEach((line, i) => {
            p.text(line, -this.width/2 + 15, -this.height/2 + 15 + (i * 20));
        });

        // Рисуем пины
        this.pins.forEach(pin => {
            // Рисуем металлические ножки
            p.stroke(180);
            p.strokeWeight(2);
            p.line(pin.offsetX, 30, pin.offsetX, pin.offsetY);
            
            // Рисуем контакты
            p.fill(pin.connected ? '#FFD700' : '#C0C0C0');
            p.stroke(0);
            p.strokeWeight(1);
            p.rect(pin.offsetX - 3, pin.offsetY - 3, 6, 6);
            
            // Подписи
            p.noStroke();
            p.fill(0);
            p.textSize(10);
            p.textAlign(p.CENTER);
            p.text(pin.name, pin.offsetX, pin.offsetY + 15);
        });

        p.pop();
    }
}

class UltrasonicSensor extends ArduinoComponent {
    constructor(x, y) {
        super(x, y);
        this.distance = 0;
        this.width = 60;
        this.height = 25;
    }

    draw(p) {
        p.push();
        p.translate(this.x, this.y);
        
        // Рисуем корпус датчика
        p.fill(0, 150, 150);
        p.rect(-this.width/2, -this.height/2, this.width, this.height);
        
        // Рисуем излучатели
        p.fill(100);
        p.ellipse(-this.width/4, 0, 15, 15);
        p.ellipse(this.width/4, 0, 15, 15);
        
        // Показываем измеренное расстояние
        p.fill(0);
        p.textSize(10);
        p.textAlign(p.CENTER);
        p.text(this.distance + " cm", 0, -this.height/2 - 5);
        
        p.pop();
    }

    measure(obstacle) {
        // Симуляция измерения расстояния
        this.distance = Math.floor(obstacle);
    }
}

class ServoMotor extends ArduinoComponent {
    constructor(x, y) {
        super(x, y);
        this.angle = 0;
        this.targetAngle = 0;
        this.size = 40;
    }

    draw(p) {
        p.push();
        p.translate(this.x, this.y);
        
        // Рисуем корпус сервопривода
        p.fill(50, 50, 200);
        p.rect(-this.size/2, -this.size/2, this.size, this.size);
        
        // Рисуем вал
        p.push();
        p.rotate(p.radians(this.angle));
        p.fill(200);
        p.rect(-2, -15, 4, 15);
        p.pop();
        
        // Показываем текущий угол
        p.fill(0);
        p.textSize(10);
        p.textAlign(p.CENTER);
        p.text(Math.floor(this.angle) + "°", 0, this.size/2 + 15);
        
        p.pop();
    }

    setAngle(angle) {
        this.targetAngle = angle;
        // Плавное вращение
        this.angle += (this.targetAngle - this.angle) * 0.1;
    }
}

class RGBLed extends ArduinoComponent {
    constructor(x, y) {
        super(x, y);
        this.r = 0;
        this.g = 0;
        this.b = 0;
        this.size = 25;
    }

    draw(p) {
        p.push();
        p.translate(this.x, this.y);
        
        // Рисуем светодиод
        p.fill(this.r, this.g, this.b);
        p.ellipse(0, 0, this.size, this.size);
        
        // Добавляем свечение
        p.noStroke();
        p.fill(this.r, this.g, this.b, 100);
        p.ellipse(0, 0, this.size * 1.5, this.size * 1.5);
        
        p.pop();
    }

    setColor(r, g, b) {
        this.r = r;
        this.g = g;
        this.b = b;
    }
}

class ArduinoBoard {
    constructor(x, y) {
        this.x = x;
        this.y = y;
        this.width = 200;
        this.height = 300;
        
        // Определяем все пины Arduino UNO
        this.pins = {
            // Цифровые пины (D0-D13)
            digital: [
                { number: 0, name: 'D0/RX', type: 'digital', x: 0, y: 0, connected: false, pwm: false },
                { number: 1, name: 'D1/TX', type: 'digital', x: 0, y: 0, connected: false, pwm: false },
                { number: 2, name: 'D2', type: 'digital', x: 0, y: 0, connected: false, pwm: false },
                { number: 3, name: 'D3~', type: 'digital', x: 0, y: 0, connected: false, pwm: true },
                { number: 4, name: 'D4', type: 'digital', x: 0, y: 0, connected: false, pwm: false },
                { number: 5, name: 'D5~', type: 'digital', x: 0, y: 0, connected: false, pwm: true },
                { number: 6, name: 'D6~', type: 'digital', x: 0, y: 0, connected: false, pwm: true },
                { number: 7, name: 'D7', type: 'digital', x: 0, y: 0, connected: false, pwm: false },
                { number: 8, name: 'D8', type: 'digital', x: 0, y: 0, connected: false, pwm: false },
                { number: 9, name: 'D9~', type: 'digital', x: 0, y: 0, connected: false, pwm: true },
                { number: 10, name: 'D10~', type: 'digital', x: 0, y: 0, connected: false, pwm: true },
                { number: 11, name: 'D11~', type: 'digital', x: 0, y: 0, connected: false, pwm: true },
                { number: 12, name: 'D12', type: 'digital', x: 0, y: 0, connected: false, pwm: false },
                { number: 13, name: 'D13', type: 'digital', x: 0, y: 0, connected: false, pwm: false }
            ],
            // Аналоговые пины (A0-A5)
            analog: [
                { number: 0, name: 'A0', type: 'analog', x: 0, y: 0, connected: false },
                { number: 1, name: 'A1', type: 'analog', x: 0, y: 0, connected: false },
                { number: 2, name: 'A2', type: 'analog', x: 0, y: 0, connected: false },
                { number: 3, name: 'A3', type: 'analog', x: 0, y: 0, connected: false },
                { number: 4, name: 'A4/SDA', type: 'analog', x: 0, y: 0, connected: false },
                { number: 5, name: 'A5/SCL', type: 'analog', x: 0, y: 0, connected: false }
            ],
            // Пины питания
            power: [
                { name: '3.3V', type: 'power3.3', x: 0, y: 0, connected: false },
                { name: '5V', type: 'power5', x: 0, y: 0, connected: false },
                { name: '5V', type: 'power5', x: 0, y: 0, connected: false },
                { name: 'GND', type: 'gnd', x: 0, y: 0, connected: false },
                { name: 'GND', type: 'gnd', x: 0, y: 0, connected: false },
                { name: 'GND', type: 'gnd', x: 0, y: 0, connected: false },
                { name: 'VIN', type: 'powerVIN', x: 0, y: 0, connected: false }
            ],
            // ICSP пины
            icsp: [
                { name: 'MISO', type: 'icsp', x: 0, y: 0, connected: false },
                { name: '5V', type: 'power5', x: 0, y: 0, connected: false },
                { name: 'SCK', type: 'icsp', x: 0, y: 0, connected: false },
                { name: 'MOSI', type: 'icsp', x: 0, y: 0, connected: false },
                { name: 'RESET', type: 'icsp', x: 0, y: 0, connected: false },
                { name: 'GND', type: 'gnd', x: 0, y: 0, connected: false }
            ]
        };
        
        this.connectedComponents = new Map(); // Хранение подключенных компонентов
        this.updatePinPositions();
    }

    updatePinPositions() {
        // Цифровые пины справа
        this.pins.digital.forEach((pin, i) => {
            pin.x = this.x + this.width - 10;
            pin.y = this.y + 50 + (i * 18);
        });

        // Аналоговые пины слева
        this.pins.analog.forEach((pin, i) => {
            pin.x = this.x + 10;
            pin.y = this.y + 50 + (i * 18);
        });

        // Пины питания сверху и снизу
        this.pins.power.forEach((pin, i) => {
            if (i < 3) {
                pin.x = this.x + 30 + (i * 30);
                pin.y = this.y + 20;
            } else {
                pin.x = this.x + 30 + ((i-3) * 30);
                pin.y = this.y + this.height - 20;
            }
        });

        // ICSP пины
        this.pins.icsp.forEach((pin, i) => {
            pin.x = this.x + this.width - 50 + ((i % 2) * 20);
            pin.y = this.y + 100 + (Math.floor(i / 2) * 20);
        });
    }

    draw(p) {
        p.push();
        
        // Рисуем плату
        p.fill(0, 150, 150);
        p.rect(this.x, this.y, this.width, this.height, 10);

        // Название платы
        p.fill(255);
        p.textSize(16);
        p.textAlign(p.CENTER, p.CENTER);
        p.text("Arduino UNO", this.x + this.width/2, this.y + 25);

        // Функция для отрисовки пина с подписью
        const drawPin = (pin, align = p.LEFT) => {
            p.fill(pin.connected ? '#FFD700' : '#C0C0C0');
            p.rect(pin.x - 3, pin.y - 3, 6, 6);
            p.fill(255);
            p.textAlign(align, p.CENTER);
            p.textSize(10);
            const textX = align === p.LEFT ? pin.x + 10 : pin.x - 10;
            p.text(pin.name, textX, pin.y);
        };

        // Отрисовка всех пинов
        this.pins.digital.forEach(pin => drawPin(pin, p.RIGHT));
        this.pins.analog.forEach(pin => drawPin(pin, p.LEFT));
        this.pins.power.forEach(pin => {
            p.textAlign(p.CENTER, p.CENTER);
            drawPin(pin, p.CENTER);
        });
        this.pins.icsp.forEach(pin => drawPin(pin, p.CENTER));

        // USB порт
        p.fill(50);
        p.rect(this.x + this.width/2 - 15, this.y - 5, 30, 10);

        p.pop();
    }

    getPinAt(x, y) {
        const checkPin = (pin) => {
            const d = Math.sqrt(Math.pow(x - pin.x, 2) + Math.pow(y - pin.y, 2));
            return d < 5 ? pin : null;
        };

        // Проверяем все типы пинов
        for (const pinGroup of Object.values(this.pins)) {
            const pin = pinGroup.map(checkPin).find(p => p);
            if (pin) return pin;
        }
        return null;
    }

    // Добавляем компонент в список подключенных
    addConnectedComponent(component, pin) {
        if (!this.connectedComponents.has(component)) {
            this.connectedComponents.set(component, new Set());
        }
        this.connectedComponents.get(component).add(pin);
    }

    // Проверяем, подключен ли компонент
    isComponentConnected(component) {
        return this.connectedComponents.has(component);
    }
}

class Wire {
    constructor(startPin, startX, startY) {
        this.startPin = startPin;
        this.startX = startX;
        this.startY = startY;
        this.endX = startX;
        this.endY = startY;
        this.connected = false;
        this.endPin = null;
        // Цвет провода зависит от типа пина
        this.color = startPin.type === 'gnd' ? '#000000' : 
                    startPin.type === 'power' ? '#FF0000' : 
                    startPin.type === 'analog' ? '#00FF00' : '#FFD700';
    }

    draw(p) {
        p.push();
        p.stroke(this.color);
        p.strokeWeight(2);
        
        // Рисуем провод с изгибом
        p.noFill();
        p.beginShape();
        p.vertex(this.startX, this.startY);
        
        // Создаем изгиб провода
        let midX = (this.startX + this.endX) / 2;
        p.bezierVertex(
            midX, this.startY,
            midX, this.endY,
            this.endX, this.endY
        );
        
        p.endShape();
        p.strokeWeight(1);
        p.pop();
    }

    updateEnd(x, y) {
        this.endX = x;
        this.endY = y;
    }

    connect(pin) {
        this.endPin = pin;
        this.connected = true;
        pin.connected = true;
        this.startPin.connected = true;
    }
} 