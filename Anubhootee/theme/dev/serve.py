"""Local static server with CORS (+ Private Network Access) so playground.wordpress.net can fetch the theme zip."""
import http.server, os, socket, sys


class H(http.server.SimpleHTTPRequestHandler):
    def end_headers(self):
        self.send_header("Access-Control-Allow-Origin", "*")
        self.send_header("Access-Control-Allow-Methods", "GET, OPTIONS")
        self.send_header("Access-Control-Allow-Headers", "*")
        self.send_header("Access-Control-Allow-Private-Network", "true")
        self.send_header("Cache-Control", "no-store")
        super().end_headers()

    def do_OPTIONS(self):
        self.send_response(204)
        self.end_headers()


class DualStack(http.server.ThreadingHTTPServer):
    address_family = socket.AF_INET6

    def server_bind(self):
        self.socket.setsockopt(socket.IPPROTO_IPV6, socket.IPV6_V6ONLY, 0)
        super().server_bind()


os.chdir(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))  # serve theme/
DualStack(("::1", int(sys.argv[1]) if len(sys.argv) > 1 else 4322), H).serve_forever()
