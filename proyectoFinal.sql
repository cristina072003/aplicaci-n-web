-- Tabla USUARIOS
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(100) NOT NULL,
    rol ENUM('admin', 'cliente') DEFAULT 'cliente',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla TESTS
CREATE TABLE tests (
    id_test INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    duracion_min INT NOT NULL
);

-- Tabla TESTS_ASIGNADOS
CREATE TABLE tests_asignados (
    id_asignacion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_test INT NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completado BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_test) REFERENCES tests(id_test) ON DELETE CASCADE
);

-- Tabla RESULTADOS
CREATE TABLE resultados (
    id_resultado INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_test INT NOT NULL,
    fecha_resultado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    puntaje_total INT,
    recomendacion TEXT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Tabla DETALLE_RESULTADOS
CREATE TABLE detalle_resultados (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_resultado INT NOT NULL,
    categoria VARCHAR(100),
    puntaje INT,
    observacion TEXT,
    FOREIGN KEY (id_resultado) REFERENCES resultados(id_resultado) ON DELETE CASCADE
);

-- Tabla PREGUNTAS
CREATE TABLE preguntas (
    id_pregunta INT AUTO_INCREMENT PRIMARY KEY,
    id_test INT NOT NULL,
    texto TEXT NOT NULL,
    tipo ENUM('opcion_multiple', 'escala_likert') NOT NULL,
    FOREIGN KEY (id_test) REFERENCES tests(id_test) ON DELETE CASCADE
);

-- Tabla OPCIONES
CREATE TABLE opciones (
    id_opcion INT AUTO_INCREMENT PRIMARY KEY,
    id_pregunta INT NOT NULL,
    texto TEXT NOT NULL,
    valor INT NOT NULL,
    FOREIGN KEY (id_pregunta) REFERENCES preguntas(id_pregunta) ON DELETE CASCADE
);

-- Insertar datos de prueba (contraseñas encriptadas)
INSERT INTO usuarios (nombre, apellido, email, password, rol) VALUES
('Admin', 'Sistema', 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'), -- Contraseña: password
('Juan', 'Pérez', 'juan@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente'), -- Contraseña: password
('María', 'Gómez', 'maria@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente'), -- Contraseña: password
('Carlos', 'López', 'carlos@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente'); -- Contraseña: password

INSERT INTO tests (id_test, nombre, descripcion, duracion_min) VALUES
(1, 'IPPR', 'Inventario de Preferencias Profesionales - Evalúa 6 áreas vocacionales con 30 preguntas tipo Likert (1-5)', 20),
(2, 'CHASIDE', 'Cuestionario de Hábitos de Estudio - 40 preguntas sobre métodos de aprendizaje y organización', 30),
(3, 'DAT-5', 'Prueba de Aptitudes Diferenciales - Evalúa 3 áreas cognitivas con 60 ítems divididos en 3 secciones', 45);

-- Preguntas IPPR (30 preguntas, todas opcion_multiple)
INSERT INTO preguntas (id_pregunta, id_test, texto, tipo) VALUES
(1, 1, 'Disfruto trabajar con números y fórmulas matemáticas', 'opcion_multiple'),
(2, 1, 'Me gusta crear obras artísticas originales', 'opcion_multiple'),
(3, 1, 'Prefiero trabajos que impliquen ayudar a otras personas', 'opcion_multiple'),
(4, 1, 'Soy bueno/a vendiendo ideas o productos', 'opcion_multiple'),
(5, 1, 'Me interesa entender cómo funcionan las máquinas', 'opcion_multiple'),
(6, 1, 'Disfruto analizando textos literarios', 'opcion_multiple'),
(7, 1, 'Me gusta realizar experimentos científicos', 'opcion_multiple'),
(8, 1, 'Prefiero expresarme a través del arte', 'opcion_multiple'),
(9, 1, 'Disfruto enseñando a otros', 'opcion_multiple'),
(10, 1, 'Soy bueno/a liderando equipos', 'opcion_multiple'),
(11, 1, 'Me gusta reparar objetos mecánicos', 'opcion_multiple'),
(12, 1, 'Disfruto discutiendo temas filosóficos', 'opcion_multiple'),
(13, 1, 'Me interesa la investigación científica', 'opcion_multiple'),
(14, 1, 'Prefiero trabajos que permitan creatividad', 'opcion_multiple'),
(15, 1, 'Me gusta trabajar con personas necesitadas', 'opcion_multiple'),
(16, 1, 'Disfruto del mundo de los negocios', 'opcion_multiple'),
(17, 1, 'Me interesa la tecnología y computación', 'opcion_multiple'),
(18, 1, 'Disfruto escribiendo poesía o narrativa', 'opcion_multiple'),
(19, 1, 'Me gusta resolver problemas matemáticos', 'opcion_multiple'),
(20, 1, 'Prefiero trabajos con libertad de expresión', 'opcion_multiple'),
(21, 1, 'Disfruto cuidando de otros', 'opcion_multiple'),
(22, 1, 'Soy bueno/a haciendo planes de negocios', 'opcion_multiple'),
(23, 1, 'Me gusta armar estructuras o modelos', 'opcion_multiple'),
(24, 1, 'Disfruto estudiando idiomas', 'opcion_multiple'),
(25, 1, 'Me interesa la astronomía y física', 'opcion_multiple'),
(26, 1, 'Prefiero trabajos con diseño gráfico', 'opcion_multiple'),
(27, 1, 'Disfruto trabajando en organizaciones sociales', 'opcion_multiple'),
(28, 1, 'Soy bueno/a en marketing y publicidad', 'opcion_multiple'),
(29, 1, 'Me gusta programar computadoras', 'opcion_multiple'),
(30, 1, 'Disfruto analizando obras literarias', 'opcion_multiple');

-- Opciones IPPR (todas iguales para ejemplo)
INSERT INTO opciones (id_pregunta, texto, valor) VALUES
-- Para cada pregunta del 1 al 30
(1, 'Muy en desacuerdo', 1), (1, 'En desacuerdo', 2), (1, 'Neutral', 3), (1, 'De acuerdo', 4), (1, 'Muy de acuerdo', 5),
(2, 'Muy en desacuerdo', 1), (2, 'En desacuerdo', 2), (2, 'Neutral', 3), (2, 'De acuerdo', 4), (2, 'Muy de acuerdo', 5),
(3, 'Muy en desacuerdo', 1), (3, 'En desacuerdo', 2), (3, 'Neutral', 3), (3, 'De acuerdo', 4), (3, 'Muy de acuerdo', 5),
(4, 'Muy en desacuerdo', 1), (4, 'En desacuerdo', 2), (4, 'Neutral', 3), (4, 'De acuerdo', 4), (4, 'Muy de acuerdo', 5),
(5, 'Muy en desacuerdo', 1), (5, 'En desacuerdo', 2), (5, 'Neutral', 3), (5, 'De acuerdo', 4), (5, 'Muy de acuerdo', 5),
(6, 'Muy en desacuerdo', 1), (6, 'En desacuerdo', 2), (6, 'Neutral', 3), (6, 'De acuerdo', 4), (6, 'Muy de acuerdo', 5),
(7, 'Muy en desacuerdo', 1), (7, 'En desacuerdo', 2), (7, 'Neutral', 3), (7, 'De acuerdo', 4), (7, 'Muy de acuerdo', 5),
(8, 'Muy en desacuerdo', 1), (8, 'En desacuerdo', 2), (8, 'Neutral', 3), (8, 'De acuerdo', 4), (8, 'Muy de acuerdo', 5),
(9, 'Muy en desacuerdo', 1), (9, 'En desacuerdo', 2), (9, 'Neutral', 3), (9, 'De acuerdo', 4), (9, 'Muy de acuerdo', 5),
(10, 'Muy en desacuerdo', 1), (10, 'En desacuerdo', 2), (10, 'Neutral', 3), (10, 'De acuerdo', 4), (10, 'Muy de acuerdo', 5),
(11, 'Muy en desacuerdo', 1), (11, 'En desacuerdo', 2), (11, 'Neutral', 3), (11, 'De acuerdo', 4), (11, 'Muy de acuerdo', 5),
(12, 'Muy en desacuerdo', 1), (12, 'En desacuerdo', 2), (12, 'Neutral', 3), (12, 'De acuerdo', 4), (12, 'Muy de acuerdo', 5),
(13, 'Muy en desacuerdo', 1), (13, 'En desacuerdo', 2), (13, 'Neutral', 3), (13, 'De acuerdo', 4), (13, 'Muy de acuerdo', 5),
(14, 'Muy en desacuerdo', 1), (14, 'En desacuerdo', 2), (14, 'Neutral', 3), (14, 'De acuerdo', 4), (14, 'Muy de acuerdo', 5),
(15, 'Muy en desacuerdo', 1), (15, 'En desacuerdo', 2), (15, 'Neutral', 3), (15, 'De acuerdo', 4), (15, 'Muy de acuerdo', 5),
(16, 'Muy en desacuerdo', 1), (16, 'En desacuerdo', 2), (16, 'Neutral', 3), (16, 'De acuerdo', 4), (16, 'Muy de acuerdo', 5),
(17, 'Muy en desacuerdo', 1), (17, 'En desacuerdo', 2), (17, 'Neutral', 3), (17, 'De acuerdo', 4), (17, 'Muy de acuerdo', 5),
(18, 'Muy en desacuerdo', 1), (18, 'En desacuerdo', 2), (18, 'Neutral', 3), (18, 'De acuerdo', 4), (18, 'Muy de acuerdo', 5),
(19, 'Muy en desacuerdo', 1), (19, 'En desacuerdo', 2), (19, 'Neutral', 3), (19, 'De acuerdo', 4), (19, 'Muy de acuerdo', 5),
(20, 'Muy en desacuerdo', 1), (20, 'En desacuerdo', 2), (20, 'Neutral', 3), (20, 'De acuerdo', 4), (20, 'Muy de acuerdo', 5),
(21, 'Muy en desacuerdo', 1), (21, 'En desacuerdo', 2), (21, 'Neutral', 3), (21, 'De acuerdo', 4), (21, 'Muy de acuerdo', 5),
(22, 'Muy en desacuerdo', 1), (22, 'En desacuerdo', 2), (22, 'Neutral', 3), (22, 'De acuerdo', 4), (22, 'Muy de acuerdo', 5),
(23, 'Muy en desacuerdo', 1), (23, 'En desacuerdo', 2), (23, 'Neutral', 3), (23, 'De acuerdo', 4), (23, 'Muy de acuerdo', 5),
(24, 'Muy en desacuerdo', 1), (24, 'En desacuerdo', 2), (24, 'Neutral', 3), (24, 'De acuerdo', 4), (24, 'Muy de acuerdo', 5),
(25, 'Muy en desacuerdo', 1), (25, 'En desacuerdo', 2), (25, 'Neutral', 3), (25, 'De acuerdo', 4), (25, 'Muy de acuerdo', 5),
(26, 'Muy en desacuerdo', 1), (26, 'En desacuerdo', 2), (26, 'Neutral', 3), (26, 'De acuerdo', 4), (26, 'Muy de acuerdo', 5),
(27, 'Muy en desacuerdo', 1), (27, 'En desacuerdo', 2), (27, 'Neutral', 3), (27, 'De acuerdo', 4), (27, 'Muy de acuerdo', 5),
(28, 'Muy en desacuerdo', 1), (28, 'En desacuerdo', 2), (28, 'Neutral', 3), (28, 'De acuerdo', 4), (28, 'Muy de acuerdo', 5),
(29, 'Muy en desacuerdo', 1), (29, 'En desacuerdo', 2), (29, 'Neutral', 3), (29, 'De acuerdo', 4), (29, 'Muy de acuerdo', 5),
(30, 'Muy en desacuerdo', 1), (30, 'En desacuerdo', 2), (30, 'Neutral', 3), (30, 'De acuerdo', 4), (30, 'Muy de acuerdo', 5);

-- Preguntas CHASIDE (40 preguntas, todas opcion_multiple)
INSERT INTO preguntas (id_pregunta, id_test, texto, tipo) VALUES
(31, 2, '¿Con qué frecuencia elaboras un plan de estudio antes de un examen?', 'opcion_multiple'),
(32, 2, 'Cuando estudias, ¿qué tanto te distraes con el celular?', 'opcion_multiple'),
(33, 2, '¿Cómo organizas tus materiales de estudio?', 'opcion_multiple'),
(34, 2, '¿Qué haces cuando no entiendes un tema?', 'opcion_multiple'),
(35, 2, '¿Cómo manejas los plazos de entrega?', 'opcion_multiple'),
(36, 2, '¿Qué tipo de ambiente prefieres para estudiar?', 'opcion_multiple'),
(37, 2, '¿Cómo tomas apuntes en clase?', 'opcion_multiple'),
(38, 2, '¿Qué haces para recordar información importante?', 'opcion_multiple'),
(39, 2, '¿Cómo preparas tus exámenes finales?', 'opcion_multiple'),
(40, 2, '¿Qué haces cuando te sientes estresado por los estudios?', 'opcion_multiple'),
(41, 2, '¿Cómo manejas las lecturas largas?', 'opcion_multiple'),
(42, 2, '¿Qué técnicas usas para comprender mejor?', 'opcion_multiple'),
(43, 2, '¿Cómo distribuyes tu tiempo de estudio?', 'opcion_multiple'),
(44, 2, '¿Qué haces para mantener la concentración?', 'opcion_multiple'),
(45, 2, '¿Cómo revisas tus trabajos antes de entregar?', 'opcion_multiple'),
(46, 2, '¿Qué haces con la información que no entiendes?', 'opcion_multiple'),
(47, 2, '¿Cómo manejas múltiples tareas?', 'opcion_multiple'),
(48, 2, '¿Qué haces para aprender vocabulario nuevo?', 'opcion_multiple'),
(49, 2, '¿Cómo preparas presentaciones orales?', 'opcion_multiple'),
(50, 2, '¿Qué haces para estudiar matemáticas?', 'opcion_multiple'),
(51, 2, '¿Cómo manejas el estrés antes de exámenes?', 'opcion_multiple'),
(52, 2, '¿Qué haces para mejorar tu escritura?', 'opcion_multiple'),
(53, 2, '¿Cómo tomas decisiones sobre qué estudiar?', 'opcion_multiple'),
(54, 2, '¿Qué haces cuando te aburres estudiando?', 'opcion_multiple'),
(55, 2, '¿Cómo manejas los trabajos en grupo?', 'opcion_multiple'),
(56, 2, '¿Qué haces para recordar fechas importantes?', 'opcion_multiple'),
(57, 2, '¿Cómo organizas tus sesiones de estudio?', 'opcion_multiple'),
(58, 2, '¿Qué haces para comprender conceptos difíciles?', 'opcion_multiple'),
(59, 2, '¿Cómo manejas la procrastinación?', 'opcion_multiple'),
(60, 2, '¿Qué haces para mantener la motivación?', 'opcion_multiple'),
(61, 2, '¿Cómo preparas tus exámenes orales?', 'opcion_multiple'),
(62, 2, '¿Qué haces para aprender de tus errores?', 'opcion_multiple'),
(63, 2, '¿Cómo manejas la carga de trabajo?', 'opcion_multiple'),
(64, 2, '¿Qué haces para estudiar eficientemente?', 'opcion_multiple'),
(65, 2, '¿Cómo manejas las distracciones?', 'opcion_multiple'),
(66, 2, '¿Qué haces para retener información?', 'opcion_multiple'),
(67, 2, '¿Cómo manejas los textos complejos?', 'opcion_multiple'),
(68, 2, '¿Qué haces para mejorar tu comprensión lectora?', 'opcion_multiple'),
(69, 2, '¿Cómo manejas el tiempo en exámenes?', 'opcion_multiple'),
(70, 2, '¿Qué haces para aprender habilidades nuevas?', 'opcion_multiple');

-- Opciones CHASIDE (todas iguales para ejemplo)
INSERT INTO opciones (id_pregunta, texto, valor) VALUES
(31, 'Nunca', 1), (31, 'Rara vez', 2), (31, 'A veces', 3), (31, 'Frecuentemente', 4), (31, 'Siempre', 5),
(32, 'Nunca', 1), (32, 'Rara vez', 2), (32, 'A veces', 3), (32, 'Frecuentemente', 4), (32, 'Siempre', 5),
(33, 'No los organizo', 1), (33, 'Por materia', 2), (33, 'Por fecha', 3), (33, 'Por prioridad', 4), (33, 'Sistema complejo', 5),
(34, 'Lo dejo para después', 1), (34, 'Pido ayuda a compañeros', 2), (34, 'Busco en internet', 3), (34, 'Consulto al profesor', 4), (34, 'Investigo en múltiples fuentes', 5),
(35, 'Poco', 1), (35, 'Moderadamente', 2), (35, 'Mucho', 3), (35, 'Constantemente', 4), (35, 'Siempre', 5),
(36, 'Silencioso', 1), (36, 'Con música', 2), (36, 'Con ruido blanco', 3), (36, 'En grupo', 4), (36, 'Solo', 5),
(37, 'Desorganizados', 1), (37, 'Por temas', 2), (37, 'Con colores', 3), (37, 'Con símbolos', 4), (37, 'Digitalmente', 5),
(38, 'Repito en voz alta', 1), (38, 'Escribo resúmenes', 2), (38, 'Hago mapas mentales', 3), (38, 'Grabo audios', 4), (38, 'Enseño a otros', 5),
(39, 'Una semana antes', 1), (39, 'Tres días antes', 2), (39, 'Un día antes', 3), (39, 'La noche anterior', 4), (39, 'No preparo', 5),
(40, 'Hago ejercicio', 1), (40, 'Medito', 2), (40, 'Escucho música', 3), (40, 'Salgo a caminar', 4), (40, 'No hago nada', 5),
(41, 'Evito', 1), (41, 'Con dificultad', 2), (41, 'Regularmente', 3), (41, 'Con facilidad', 4), (41, 'Sin problemas', 5),
(42, 'No uso técnicas', 1), (42, 'Subrayado', 2), (42, 'Esquemas', 3), (42, 'Resúmenes', 4), (42, 'Mapas mentales', 5),
(43, 'Menos de 1 hora', 1), (43, '1-2 horas', 2), (43, '2-3 horas', 3), (43, '3-4 horas', 4), (43, 'Más de 4 horas', 5),
(44, 'Me distraigo fácilmente', 1), (44, 'Concentración media', 2), (44, 'Concentrado', 3), (44, 'Muy concentrado', 4), (44, 'En estado de flujo', 5),
(45, 'Nunca reviso', 1), (45, 'Rara vez', 2), (45, 'A veces', 3), (45, 'Frecuentemente', 4), (45, 'Siempre', 5),
(46, 'Tiro todo', 1), (46, 'Guardo por si acaso', 2), (46, 'Pregunto a otros', 3), (46, 'Busco ayuda', 4), (46, 'Investigo', 5),
(47, 'Una sola tarea', 1), (47, 'Dos tareas', 2), (47, 'Tres tareas', 3), (47, 'Cuatro tareas', 4), (47, 'Cinco o más tareas', 5),
(48, 'No me interesa', 1), (48, 'Con diccionario', 2), (48, 'Con aplicaciones', 3), (48, 'Con tarjetas', 4), (48, 'Con inmersión', 5),
(49, 'No hago presentaciones', 1), (49, 'Con ayuda', 2), (49, 'Practicando', 3), (49, 'Con método sistemático', 4), (49, 'Integrando múltiples enfoques', 5),
(50, 'Evito aprenderlas', 1), (50, 'Con ayuda', 2), (50, 'Practicando', 3), (50, 'Con método sistemático', 4), (50, 'Integrando múltiples enfoques', 5),
(51, 'No me estreso', 1), (51, 'Con dificultad', 2), (51, 'Regularmente', 3), (51, 'Con facilidad', 4), (51, 'Sin problemas', 5),
(52, 'No escribo', 1), (52, 'Con errores', 2), (52, 'Regularmente', 3), (52, 'Bien', 4), (52, 'Excelente', 5),
(53, 'Sin criterio', 1), (53, 'Por materias', 2), (53, 'Por interés', 3), (53, 'Por orientación', 4), (53, 'Por vocación', 5),
(54, 'Busco distracciones', 1), (54, 'Cambio de actividad', 2), (54, 'Tomo un descanso', 3), (54, 'Sigo intentando', 4), (54, 'Me enfoco más', 5),
(55, 'Evito trabajar en grupo', 1), (55, 'Con dificultad', 2), (55, 'Regularmente', 3), (55, 'Bien', 4), (55, 'Excelente', 5),
(56, 'No recuerdo fechas', 1), (56, 'Con dificultad', 2), (56, 'Regularmente', 3), (56, 'Bien', 4), (56, 'Excelente', 5),
(57, 'Desorganizado', 1), (57, 'Algo organizado', 2), (57, 'Regularmente', 3), (57, 'Bien', 4), (57, 'Muy organizado', 5),
(58, 'No comprendo', 1), (58, 'Con dificultad', 2), (58, 'Regularmente', 3), (58, 'Bien', 4), (58, 'Excelente', 5),
(59, 'Procrastino siempre', 1), (59, 'A menudo', 2), (59, 'A veces', 3), (59, 'Rara vez', 4), (59, 'Nunca', 5),
(60, 'Sin motivación', 1), (60, 'Con dificultad', 2), (60, 'Regularmente', 3), (60, 'Bien', 4), (60, 'Excelente', 5),
(61, 'No preparo', 1), (61, 'Con ayuda', 2), (61, 'Practicando', 3), (61, 'Con método sistemático', 4), (61, 'Integrando múltiples enfoques', 5),
(62, 'No aprendo de mis errores', 1), (62, 'Con dificultad', 2), (62, 'Regularmente', 3), (62, 'Bien', 4), (62, 'Excelente', 5),
(63, 'Sobrecargado', 1), (63, 'Con dificultad', 2), (63, 'Regularmente', 3), (63, 'Bien', 4), (63, 'Excelente', 5),
(64, 'No estudio', 1), (64, 'Con dificultad', 2), (64, 'Regularmente', 3), (64, 'Bien', 4), (64, 'Excelente', 5),
(65, 'Siempre distraído', 1), (65, 'A menudo', 2), (65, 'A veces', 3), (65, 'Rara vez', 4), (65, 'Nunca', 5),
(66, 'No retengo nada', 1), (66, 'Con dificultad', 2), (66, 'Regularmente', 3), (66, 'Bien', 4), (66, 'Excelente', 5),
(67, 'No entiendo nada', 1), (67, 'Con dificultad', 2), (67, 'Regularmente', 3), (67, 'Bien', 4), (67, 'Excelente', 5),
(68, 'No mejoro', 1), (68, 'Con dificultad', 2), (68, 'Regularmente', 3), (68, 'Bien', 4), (68, 'Excelente', 5),
(69, 'Siempre tarde', 1), (69, 'A menudo', 2), (69, 'A veces', 3), (69, 'Rara vez', 4), (69, 'Nunca', 5),
(70, 'No aprendo', 1), (70, 'Con dificultad', 2), (70, 'Regularmente', 3), (70, 'Bien', 4), (70, 'Excelente', 5);

-- Preguntas DAT-5 (60 preguntas, todas opcion_multiple)
INSERT INTO preguntas (id_pregunta, id_test, texto, tipo) VALUES
(71, 3, 'Sinónimo de "efímero":', 'opcion_multiple'),
(72, 3, 'Antónimo de "lucidez":', 'opcion_multiple'),
(73, 3, 'Analogía: libro es a leer como disco es a...', 'opcion_multiple'),
(74, 3, 'Complete: "Más vale pájaro en mano que..."', 'opcion_multiple'),
(75, 3, 'Palabra que no pertenece al grupo:', 'opcion_multiple'),
(76, 3, 'Significado de "inefable":', 'opcion_multiple'),
(77, 3, 'Analogía: médico es a estetoscopio como juez es a...', 'opcion_multiple'),
(78, 3, 'Sinónimo de "perspicaz":', 'opcion_multiple'),
(79, 3, 'Complete la serie: silla, mesa, sofá, ___', 'opcion_multiple'),
(80, 3, 'Antónimo de "pródigo":', 'opcion_multiple'),
(81, 3, 'Analogía: poeta es a poema como compositor es a...', 'opcion_multiple'),
(82, 3, 'Significado de "sesgado":', 'opcion_multiple'),
(83, 3, 'Complete: "No hay mal que por bien no..."', 'opcion_multiple'),
(84, 3, 'Palabra que no pertenece:', 'opcion_multiple'),
(85, 3, 'Analogía: pintor es a lienzo como escultor es a...', 'opcion_multiple'),
(86, 3, 'Sinónimo de "incólume":', 'opcion_multiple'),
(87, 3, 'Antónimo de "acuciante":', 'opcion_multiple'),
(88, 3, 'Complete la serie: novela, cuento, fábula, ___', 'opcion_multiple'),
(89, 3, 'Significado de "proclive":', 'opcion_multiple'),
(90, 3, 'Analogía: arquitecto es a edificio como ingeniero es a...', 'opcion_multiple'),
(91, 3, 'Complete la serie: 2, 4, 8, 16, ___', 'opcion_multiple'),
(92, 3, 'Resuelva: (15 × 3) + (12 ÷ 4) =', 'opcion_multiple'),
(93, 3, 'Qué número sigue: 5, 10, 9, 18, 17, 34, ___', 'opcion_multiple'),
(94, 3, 'Si 3 lápices cuestan $1.20, ¿cuánto cuestan 10?', 'opcion_multiple'),
(95, 3, 'Porcentaje: 25% de 200 es', 'opcion_multiple'),
(96, 3, 'Resuelva: 4² + √81 - 5 =', 'opcion_multiple'),
(97, 3, 'Patrón: 1, 3, 6, 10, 15, ___', 'opcion_multiple'),
(98, 3, 'Si 5 trabajadores hacen un muro en 6 horas, ¿cuánto tardan 3?', 'opcion_multiple'),
(99, 3, 'Fracciones: 1/2 + 1/4 + 1/8 =', 'opcion_multiple'),
(100, 3, 'Álgebra: Si x + 5 = 12, entonces x =', 'opcion_multiple'),
(101, 3, 'Geometría: Área de un rectángulo 6cm × 8cm', 'opcion_multiple'),
(102, 3, 'Promedio de: 12, 15, 18, 21', 'opcion_multiple'),
(103, 3, 'Razón: Si a:b = 3:5 y b=20, entonces a=', 'opcion_multiple'),
(104, 3, 'Ecuación: 2x - 5 = 15', 'opcion_multiple'),
(105, 3, 'Porcentaje aumento: De 80 a 100 es', 'opcion_multiple'),
(106, 3, 'Probabilidad: Al lanzar un dado, chance de número par', 'opcion_multiple'),
(107, 3, 'Tiempo: Si son las 3:15, qué hora será en 45 minutos', 'opcion_multiple'),
(108, 3, 'Volumen: Cubo con lado 3cm', 'opcion_multiple'),
(109, 3, 'Descuento: Precio original $120, 25% off', 'opcion_multiple'),
(110, 3, 'Interés simple: $1000 al 5% anual por 2 años', 'opcion_multiple'),
(111, 3, 'Identifique el patrón en las figuras:', 'opcion_multiple'),
(112, 3, 'Qué figura completa la serie:', 'opcion_multiple'),
(113, 3, 'Identifique la figura diferente:', 'opcion_multiple'),
(114, 3, 'Qué figura sigue en la secuencia:', 'opcion_multiple'),
(115, 3, 'Rotación de figuras: cuál es la correcta', 'opcion_multiple'),
(116, 3, 'Patrón de formas y sombras:', 'opcion_multiple'),
(117, 3, 'Identifique el cubo desarrollado correctamente:', 'opcion_multiple'),
(118, 3, 'Simetría: seleccione la opción simétrica', 'opcion_multiple'),
(119, 3, 'Analogía visual: figura A es a B como C es a...', 'opcion_multiple'),
(120, 3, 'Qué figura no pertenece al grupo:', 'opcion_multiple'),
(121, 3, 'Complete el patrón de puntos:', 'opcion_multiple'),
(122, 3, 'Identifique la figura rotada:', 'opcion_multiple'),
(123, 3, 'Secuencia lógica de formas:', 'opcion_multiple'),
(124, 3, 'Qué figura completa el conjunto:', 'opcion_multiple'),
(125, 3, 'Patrón de figuras geométricas:', 'opcion_multiple'),
(126, 3, 'Identifique la pieza faltante:', 'opcion_multiple'),
(127, 3, 'Transformación de figuras:', 'opcion_multiple'),
(128, 3, 'Qué figura sigue en la serie 3D:', 'opcion_multiple'),
(129, 3, 'Patrón de sombreado:', 'opcion_multiple'),
(130, 3, 'Identifique la figura espejo:', 'opcion_multiple');

-- Opciones DAT-5 (todas iguales para ejemplo)
INSERT INTO opciones (id_pregunta, texto, valor) VALUES
(71, 'Opción A', 1), (71, 'Opción B', 2), (71, 'Opción C', 3), (71, 'Opción D', 4),
(72, 'Opción A', 1), (72, 'Opción B', 2), (72, 'Opción C', 3), (72, 'Opción D', 4),
(73, 'Opción A', 1), (73, 'Opción B', 2), (73, 'Opción C', 3), (73, 'Opción D', 4),
(74, 'Opción A', 1), (74, 'Opción B', 2), (74, 'Opción C', 3), (74, 'Opción D', 4),
(75, 'Opción A', 1), (75, 'Opción B', 2), (75, 'Opción C', 3), (75, 'Opción D', 4),
(76, 'Opción A', 1), (76, 'Opción B', 2), (76, 'Opción C', 3), (76, 'Opción D', 4),
(77, 'Opción A', 1), (77, 'Opción B', 2), (77, 'Opción C', 3), (77, 'Opción D', 4),
(78, 'Opción A', 1), (78, 'Opción B', 2), (78, 'Opción C', 3), (78, 'Opción D', 4),
(79, 'Opción A', 1), (79, 'Opción B', 2), (79, 'Opción C', 3), (79, 'Opción D', 4),
(80, 'Opción A', 1), (80, 'Opción B', 2), (80, 'Opción C', 3), (80, 'Opción D', 4),
(81, 'Opción A', 1), (81, 'Opción B', 2), (81, 'Opción C', 3), (81, 'Opción D', 4),
(82, 'Opción A', 1), (82, 'Opción B', 2), (82, 'Opción C', 3), (82, 'Opción D', 4),
(83, 'Opción A', 1), (83, 'Opción B', 2), (83, 'Opción C', 3), (83, 'Opción D', 4),
(84, 'Opción A', 1), (84, 'Opción B', 2), (84, 'Opción C', 3), (84, 'Opción D', 4),
(85, 'Opción A', 1), (85, 'Opción B', 2), (85, 'Opción C', 3), (85, 'Opción D', 4),
(86, 'Opción A', 1), (86, 'Opción B', 2), (86, 'Opción C', 3), (86, 'Opción D', 4),
(87, 'Opción A', 1), (87, 'Opción B', 2), (87, 'Opción C', 3), (87, 'Opción D', 4),
(88, 'Opción A', 1), (88, 'Opción B', 2), (88, 'Opción C', 3), (88, 'Opción D', 4),
(89, 'Opción A', 1), (89, 'Opción B', 2), (89, 'Opción C', 3), (89, 'Opción D', 4),
(90, 'Opción A', 1), (90, 'Opción B', 2), (90, 'Opción C', 3), (90, 'Opción D', 4),
(91, 'Opción A', 1), (91, 'Opción B', 2), (91, 'Opción C', 3), (91, 'Opción D', 4),
(92, 'Opción A', 1), (92, 'Opción B', 2), (92, 'Opción C', 3), (92, 'Opción D', 4),
(93, 'Opción A', 1), (93, 'Opción B', 2), (93, 'Opción C', 3), (93, 'Opción D', 4),
(94, 'Opción A', 1), (94, 'Opción B', 2), (94, 'Opción C', 3), (94, 'Opción D', 4),
(95, 'Opción A', 1), (95, 'Opción B', 2), (95, 'Opción C', 3), (95, 'Opción D', 4),
(96, 'Opción A', 1), (96, 'Opción B', 2), (96, 'Opción C', 3), (96, 'Opción D', 4),
(97, 'Opción A', 1), (97, 'Opción B', 2), (97, 'Opción C', 3), (97, 'Opción D', 4),
(98, 'Opción A', 1), (98, 'Opción B', 2), (98, 'Opción C', 3), (98, 'Opción D', 4),
(99, 'Opción A', 1), (99, 'Opción B', 2), (99, 'Opción C', 3), (99, 'Opción D', 4),
(100, 'Opción A', 1), (100, 'Opción B', 2), (100, 'Opción C', 3), (100, 'Opción D', 4),
(101, 'Opción A', 1), (101, 'Opción B', 2), (101, 'Opción C', 3), (101, 'Opción D', 4),
(102, 'Opción A', 1), (102, 'Opción B', 2), (102, 'Opción C', 3), (102, 'Opción D', 4),
(103, 'Opción A', 1), (103, 'Opción B', 2), (103, 'Opción C', 3), (103, 'Opción D', 4),
(104, 'Opción A', 1), (104, 'Opción B', 2), (104, 'Opción C', 3), (104, 'Opción D', 4),
(105, 'Opción A', 1), (105, 'Opción B', 2), (105, 'Opción C', 3), (105, 'Opción D', 4),
(106, 'Opción A', 1), (106, 'Opción B', 2), (106, 'Opción C', 3), (106, 'Opción D', 4),
(107, 'Opción A', 1), (107, 'Opción B', 2), (107, 'Opción C', 3), (107, 'Opción D', 4),
(108, 'Opción A', 1), (108, 'Opción B', 2), (108, 'Opción C', 3), (108, 'Opción D', 4),
(109, 'Opción A', 1), (109, 'Opción B', 2), (109, 'Opción C', 3), (109, 'Opción D', 4),
(110, 'Opción A', 1), (110, 'Opción B', 2), (110, 'Opción C', 3), (110, 'Opción D', 4),
(111, 'Opción A', 1), (111, 'Opción B', 2), (111, 'Opción C', 3), (111, 'Opción D', 4),
(112, 'Opción A', 1), (112, 'Opción B', 2), (112, 'Opción C', 3), (112, 'Opción D', 4),
(113, 'Opción A', 1), (113, 'Opción B', 2), (113, 'Opción C', 3), (113, 'Opción D', 4),
(114, 'Opción A', 1), (114, 'Opción B', 2), (114, 'Opción C', 3), (114, 'Opción D', 4),
(115, 'Opción A', 1), (115, 'Opción B', 2), (115, 'Opción C', 3), (115, 'Opción D', 4),
(116, 'Opción A', 1), (116, 'Opción B', 2), (116, 'Opción C', 3), (116, 'Opción D', 4),
(117, 'Opción A', 1), (117, 'Opción B', 2), (117, 'Opción C', 3), (117, 'Opción D', 4),
(118, 'Opción A', 1), (118, 'Opción B', 2), (118, 'Opción C', 3), (118, 'Opción D', 4),
(119, 'Opción A', 1), (119, 'Opción B', 2), (119, 'Opción C', 3), (119, 'Opción D', 4),
(120, 'Opción A', 1), (120, 'Opción B', 2), (120, 'Opción C', 3), (120, 'Opción D', 4),
(121, 'Opción A', 1), (121, 'Opción B', 2), (121, 'Opción C', 3), (121, 'Opción D', 4),
(122, 'Opción A', 1), (122, 'Opción B', 2), (122, 'Opción C', 3), (122, 'Opción D', 4),
(123, 'Opción A', 1), (123, 'Opción B', 2), (123, 'Opción C', 3), (123, 'Opción D', 4),
(124, 'Opción A', 1), (124, 'Opción B', 2), (124, 'Opción C', 3), (124, 'Opción D', 4),
(125, 'Opción A', 1), (125, 'Opción B', 2), (125, 'Opción C', 3), (125, 'Opción D', 4),
(126, 'Opción A', 1), (126, 'Opción B', 2), (126, 'Opción C', 3), (126, 'Opción D', 4),
(127, 'Opción A', 1), (127, 'Opción B', 2), (127, 'Opción C', 3), (127, 'Opción D', 4),
(128, 'Opción A', 1), (128, 'Opción B', 2), (128, 'Opción C', 3), (128, 'Opción D', 4),
(129, 'Opción A', 1), (129, 'Opción B', 2), (129, 'Opción C', 3), (129, 'Opción D', 4),
(130, 'Opción A', 1), (130, 'Opción B', 2), (130, 'Opción C', 3), (130, 'Opción D', 4);

-- Asignar tests a usuarios
INSERT INTO tests_asignados (id_usuario, id_test, fecha_asignacion, completado) VALUES
(2, 1, '2025-05-10 09:00:00', TRUE),
(2, 2, '2025-05-12 10:30:00', FALSE),
(3, 1, '2025-05-15 14:00:00', TRUE),
(4, 3, '2025-05-18 11:15:00', TRUE),
(3, 2, '2025-05-20 16:45:00', FALSE),
(4, 1, '2025-05-22 10:00:00', TRUE),
(2, 3, '2025-05-25 13:30:00', FALSE),
(3, 3, '2025-05-28 09:15:00', TRUE),
(4, 2, '2025-06-01 15:00:00', FALSE);

-- Resultados de ejemplo
INSERT INTO resultados (id_usuario, id_test, fecha_resultado, puntaje_total, recomendacion) VALUES
(2, 1, '2025-05-10 09:45:00', 85, 'Perfil técnico-científico marcado. Recomendadas ingenierías o ciencias exactas.'),
(3, 1, '2025-05-15 14:45:00', 78, 'Perfil social-artístico. Recomendadas carreras humanísticas o de ayuda social.'),
(4, 3, '2025-05-18 12:30:00', 92, 'Excelente razonamiento lógico. Aptitudes destacadas en todas las áreas.'),
(4, 1, '2025-05-22 10:50:00', 88, 'Perfil equilibrado con inclinación científica. Buen potencial para investigación.'),
(3, 3, '2025-05-28 10:15:00', 84, 'Buen desempeño en razonamiento abstracto. Aptitud para carreras creativas.');

-- Detalle de resultados (nuevo modelo: id_resultado, categoria, puntaje, observacion)
INSERT INTO detalle_resultados (id_resultado, categoria, puntaje, observacion) VALUES
(1, 'Matemáticas', 15, 'Buen desempeño en lógica matemática'),
(1, 'Ciencias', 18, 'Interés en experimentos científicos'),
(1, 'Arte', 12, 'Creatividad media'),
(2, 'Social', 20, 'Alta empatía y ayuda a otros'),
(2, 'Negocios', 14, 'Liderazgo y ventas'),
(3, 'Verbal', 30, 'Excelente comprensión verbal'),
(3, 'Numérico', 32, 'Razonamiento numérico alto'),
(3, 'Abstracto', 30, 'Muy buen razonamiento abstracto'),
(4, 'Ciencias', 20, 'Potencial para investigación'),
(4, 'Tecnología', 18, 'Interés en computación'),
(5, 'Abstracto', 28, 'Creatividad destacada'),
(5, 'Numérico', 30, 'Buen desempeño en lógica'),
(5, 'Verbal', 26, 'Comprensión lectora sólida');