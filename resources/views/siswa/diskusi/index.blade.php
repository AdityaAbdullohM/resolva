@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Daftar Diskusi</div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Judul Diskusi</th>
                                    <th>Kelas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($discussions as $discussion)
                                    <tr>
                                        <td>{{ $discussion->title }}</td>
                                        <td>{{ optional($discussion->problem->kelas)->nama ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('mahasiswa.discussions.show', $discussion) }}" class="btn btn-primary">Lihat</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
