<?php

class Lxd
{

    static public function init($request)
    {
        $request = $request->get();
        $id = 'inst-' . $request['id'];
        $image = $request['image'];
        $disk = $request['disk'];
        $cpu = $request['cpu'];
        $mem = $request['mem'];
        $password = $request['password'];

        console("Initing instance {$id} using {$image}");

        exec("lxc init {$image} {$id}");

        echo "Setting up CPU.      ";
        exec("lxc config set {$id} limits.cpu {$cpu}");

        echo "Setting up Memory.      ";
        exec("lxc config set {$id} limits.memory {$mem}MB");

        echo "Setting up auto start.      ";
        exec("lxc config set {$id} boot.autostart true");


        echo "Setting up disk.      ";
        exec("lxc config device override {$id} root size={$disk}GB");

        echo "Starting {$id}.      ";
        exec("lxc start {$id}");

        echo "Setting up password.     ";
        $shell = "echo \"root:{$password}\" | lxc exec {$id} chpasswd";
        exec($shell);

        echo "Restarting {$id}      ";
        exec("lxc restart {$id}");


        for ($i = 0; $i <= 50; $i++) {
            echo PHP_EOL . "[{$i}] " . 'Trying ipv4...';
            $ip = exec("lxc list | grep {$id} | egrep -o \"[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+\"");
            if ($ip == '' || is_null($ip) || $ip == ' ') {
                sleep(1);
            } else {
                console("Instance {$id} ipv4 is {$ip}");
                break;
            }

            if ($i == 50) {
                return ['status' => 0, 'id' => $id];
            }
        }


        console("Inited instance {$id}");

        return [
            'status' => 1,
            'id' => $id,
            'lan_ip' => $ip
        ];
    }

    static public function start($id)
    {
        exec("lxc start inst-{$id} >/dev/null 2>&1");
        return [
            'status' => 1,
            'id' => $id
        ];
    }

    static public function stop($id)
    {
        exec("lxc stop inst-{$id}");
        return [
            'status' => 1,
            'lan_ip' => $id
        ];
    }

    static public function delete($id)
    {
        console("Deleting instance {$id}...");
        exec("lxc delete inst-{$id} --force");
        return [
            'status' => 1
        ];
    }


    static public function forward($request)
    {
        $request = $request->get();
        $id = 'inst-' . $request['id'];
        $from = $request['from'];
        $to = $request['to'];
        $name = 'proxy-' . $to;

        $shell = "lxc config device add {$id} {$name}-tcp proxy listen=tcp:0.0.0.0:{$to} connect=tcp:127.0.0.1:{$from}";
        $exec = exec($shell);
        echo $exec;
        $shell = "lxc config device add {$id} {$name}-udp proxy listen=udp:0.0.0.0:{$to} connect=udp:127.0.0.1:{$from}";
        $exec = exec($shell);
        echo $exec;

        console("Forwarding port from {$from} to {$to}, name {$name}-tcp and {$name}-udp");

        return [
            'status' => 1,
        ];
    }

    static public function forward_delete($request)
    {
        $request = $request->get();

        $id = 'inst-' . $request['id'];

        $to = $request['to'];
        $name = 'proxy-' . $to;

        $shell = "lxc config device remove {$id} {$name}-tcp";
        $exec = exec($shell);
        echo $exec;
        $shell = "lxc config device remove {$id} {$name}-udp";
        $exec = exec($shell);
        echo $exec;


        console("Removed forward port {$to} by {$id}, name {$name}-tcp and {$name}-udp");

        return [
            'status' => 1,
        ];
    }


    static public function resize($request)
    {
        $request = $request->get();
        $id = 'inst-' . $request['id'];
        $disk = $request['disk'];
        $cpu = $request['cpu'];
        $mem = $request['mem'];

        console("Resizing {$id}, CPU {$cpu}, Memory {$mem}, Disk {$disk}");

        echo "Updating CPU.      ";
        exec("lxc config set {$id} limits.cpu {$cpu}");

        echo "Updating Memory.      ";
        exec("lxc config set {$id} limits.memory {$mem}MB");

        echo "Updating disk.      ";
        exec("lxc config device override {$id} root size={$disk}GB");

        return [
            'status' => 1,
        ];
    }
}
