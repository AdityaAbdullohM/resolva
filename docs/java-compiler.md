# Penjelasan: Compiler Java

Dokumentasi singkat ini menjelaskan apa itu compiler Java, bagaimana alur kerjanya, dan beberapa konsep terkait seperti bytecode, JVM, dan JIT.

## Ringkasan

Compiler Java (`javac`) menerjemahkan kode sumber Java (.java) menjadi bytecode yang tersimpan di file `.class`. Bytecode ini bukan kode mesin untuk CPU tertentu — melainkan representasi tingkat-menengah yang dieksekusi oleh Java Virtual Machine (JVM).

## Tahapan kerja compiler

- Lexical analysis: memecah teks sumber menjadi token (kata kunci, identifier, literal, simbol).
- Syntax analysis (parsing): membangun pohon sintaks (AST) dari token dan memeriksa struktur bahasa.
- Semantic analysis: melakukan pengecekan tipe, resolusi nama, dan aturan bahasa lain (mis. akses modifier, finality).
- Intermediate representation: compiler menghasilkan representasi antara (bytecode) untuk JVM.
- Optimization: beberapa optimasi tingkat compile-time dapat diterapkan, namun kebanyakan optimasi dilakukan di runtime oleh JIT.
- Code generation: hasil akhirnya adalah file `.class` yang berisi bytecode dan metadata (constant pool, descriptor, atribut).

## Bytecode dan file `.class`

File `.class` berisi instruksi bytecode yang diinterpretasikan atau dikompilasi-jit oleh JVM. Bytecode dirancang untuk mudah dianalisis dan dijalankan oleh mesin virtual, memungkinkan portabilitas antar platform.

## Peran JVM

JVM memuat kelas (`ClassLoader`), memverifikasi bytecode (bytecode verifier), dan mengeksekusi bytecode. Eksekusi bisa melalui interpreter atau Just-In-Time (JIT) compiler yang mengkompilasi hotspot bytecode menjadi kode mesin native untuk meningkatkan performa.

## Perbedaan `javac` vs JIT

- `javac`: compiler statis yang menerjemahkan .java → .class (bytecode). Tidak menghasilkan kode mesin spesifik CPU.
- JIT (bagian dari JVM): mengkompilasi sebagian bytecode yang sering dijalankan menjadi kode mesin saat runtime, memungkinkan optimasi berbasis profil.

## Contoh perintah umum

- Kompilasi satu file:

  javac HelloWorld.java

- Menjalankan program (menggunakan `java` untuk memulai JVM):

  java HelloWorld

- Mengatur classpath:

  javac -cp libs/*:src -d out src/com/example/*.java

Catatan: pada Windows, gunakan `;` sebagai pemisah classpath.

## Error umum dan penanganan

- Error kompilasi (syntax/typing): periksa pesan `javac` dan perbaiki baris yang dilaporkan.
- NoClassDefFoundError / ClassNotFoundException: periksa `-cp`/classpath dan struktur paket.
- UnsupportedClassVersionError: versi JRE lebih lama daripada versi target yang digunakan saat kompilasi (solusi: gunakan `--release` atau cocokkan versi javac/jre).

## Keamanan dan verifikasi

JVM melakukan verifikasi bytecode untuk mencegah instruksi berbahaya atau tidak valid (mis. pelanggaran tipe). Ini adalah lapisan keamanan penting, terutama saat memuat kode dari sumber tak-tepercaya.

## Ringkasan singkat untuk pengembang

- Tulis `.java` → gunakan `javac` untuk membuat `.class` → jalankan dengan `java` (JVM) yang melakukan pemuatan, verifikasi, dan eksekusi.
- Untuk performa, fokus pada algoritma dan struktur data; biarkan JIT melakukan optimasi runtime.

## Referensi singkat

- Dokumentasi resmi: https://docs.oracle.com/javase/8/docs/
- Tutorial `javac` dan `java`: https://docs.oracle.com/javase/tutorial/

## Tracer (ringkas)

Tracer adalah alat yang merekam jejak eksekusi program — mis. pemanggilan fungsi, urutan eksekusi, dan waktu eksekusi. Tracer berguna untuk debugging, profiling, dan analisis performa.

Beberapa pendekatan tracer di ekosistem Java:

- Instrumentation/agent: menyisipkan kode (bytecode weaving) menggunakan `-javaagent:agent.jar` atau Java Instrumentation API.
- JVM tooling: menggunakan alat bawaan seperti `jstack`, `jmap`, `jcmd`, atau profiler (mis. `async-profiler`) untuk sampling dan hot-spot analysis.
- Debugger/tracer source-level: memakai protocol debug (JDWP) untuk melangkah (step) dan memeriksa state saat runtime.

Perbedaan singkat antara compiler dan tracer:

- Compiler: menerjemahkan kode sumber menjadi bytecode (statik), bertugas sebelum program dijalankan.
- Tracer: merekam perilaku saat program berjalan (dinamis), membantu analisis runtime, debugging, dan optimasi berbasis profil.

Contoh singkat opsi runtime:

- Menjalankan dengan agent instrumentasi:

  java -javaagent:agent.jar -jar app.jar

- Menjalankan debugger remote (JDWP):

  java -agentlib:jdwp=transport=dt_socket,server=y,suspend=n,address=*:5005 -jar app.jar

